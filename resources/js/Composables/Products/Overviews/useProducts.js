import { ref, reactive, computed } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

export function useProducts() {
    const products = ref([]);
    const searchQuery = ref("");
    const loading = ref(false);
    const filters = reactive({
        manufacturerId: "",
        positionId: "",
        productType: "",
        fromDate: null,
        toDate: null,
    });

    const pagination = reactive({
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0,
    });



    const filteredProducts = computed(() => {
        if (!searchQuery.value || !searchQuery.value.trim()) {
            return products.value;
        }
        const term = searchQuery.value.toLowerCase().trim();
        return products.value.filter((product) => {
            const name = (
                product.ten_thuoc ||
                product.ten_hang_hoa ||
                ""
            ).toLowerCase();
            const code = (product.ma_hang || "").toLowerCase();
            return name.includes(term) || code.includes(term);
        });
    });

    const formatDateToLocal = (dateValue) => {
        if (!dateValue) return null;
        const date = new Date(dateValue);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    };

    const loadProducts = async (selectedCategoryId = null) => {
        loading.value = true;
        try {
            const fromDate = formatDateToLocal(filters.fromDate);
            const toDate = formatDateToLocal(filters.toDate);

            const [medicinesResponse, goodsResponse] = await Promise.all([
                axios.get("/admin/medicines/api", {
                    params: {
                        search: searchQuery.value,
                        category_id: selectedCategoryId,
                        from_date: fromDate,
                        to_date: toDate,
                        per_page: pagination.per_page,
                        page: pagination.current_page,
                    },
                }),
                axios.get("/admin/goods/api", {
                    params: {
                        search: searchQuery.value,
                        category_id: selectedCategoryId,
                        from_date: fromDate,
                        to_date: toDate,
                        per_page: pagination.per_page,
                        page: pagination.current_page,
                    },
                }),
            ]);

            let allProducts = [];
            let totalMedicines = 0;
            let totalGoods = 0;

            if (medicinesResponse.data.success) {
                const mData = medicinesResponse.data.data;
                const mList = mData?.data || mData || [];
                totalMedicines = mData?.total || mList.length;

                const medicines = mList.map((m) => ({
                    ...m,
                    product_type: "medicine",
                    ten_thuoc: m.ten_thuoc,
                    ten_hang_hoa: m.ten_thuoc,
                }));
                allProducts = [...allProducts, ...medicines];
            }

            if (goodsResponse.data.success) {
                const gData = goodsResponse.data.data;
                const gList = gData?.data || gData || [];
                totalGoods = gData?.total || gList.length;

                const goods = gList.map((g) => ({
                    ...g,
                    product_type: "goods",
                    ten_thuoc: g.ten_hang_hoa,
                    ten_hang_hoa: g.ten_hang_hoa,
                }));
                allProducts = [...allProducts, ...goods];
            }

            products.value = allProducts.sort(
                (a, b) => new Date(b.created_at) - new Date(a.created_at),
            );

            pagination.total = totalMedicines + totalGoods;
            pagination.last_page = Math.ceil(
                pagination.total / pagination.per_page,
            );

        } catch (error) {
            console.error("Error loading products:", error);
            products.value = [];
        } finally {
            loading.value = false;
        }
    };



    const deleteProduct = (product) => {
        const productName =
            product.ten_thuoc || product.ten_hang_hoa || "sản phẩm";
        if (!confirm(`Bạn có chắc muốn xóa ${productName}?`)) return false;

        let url = "";
        const type = product.product_type;
        const id = product.id;

        if (type === "medicine") url = `/admin/medicines/${id}`;
        else if (type === "goods") url = `/admin/goods/${id}`;
        else if (type === "service") url = `/admin/services/${id}`;

        if (!url) {
            console.error("Không tìm thấy loại sản phẩm để xóa:", product);
            alert("Không thể xác định loại sản phẩm để xóa!");
            return false;
        }

        router.delete(url, {
            onSuccess: () => {
                // Remove from local list to avoid waiting for another manual fetch
                products.value = products.value.filter(
                    (p) => !(p.id === id && p.product_type === type),
                );
            },
            onError: (errors) => {
                console.error("Lỗi khi xóa sản phẩm:", errors);
                alert("Có lỗi xảy ra khi xóa sản phẩm!");
            },
        });
    };

    return {
        products,
        searchQuery,
        loading,
        filters,
        pagination,
        filteredProducts,
        loadProducts,
        deleteProduct,
    };
}
