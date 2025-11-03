/**
 * Province Service - Shared service for province/ward API calls
 * Sử dụng cho cả Supplier và Doctor
 */
class ProvinceService {
    constructor() {
        this.baseUrl = 'https://provinces.open-api.vn/api';
        this.baseUrlHttp = 'http://provinces.open-api.vn/api'; // Fallback HTTP endpoint
        this.cache = {
            provinces: null,
            wards: new Map() // Cache wards by province code
        };
    }

    /**
     * Helper function để fetch với fallback HTTP khi HTTPS bị lỗi SSL
     * @param {string} url - URL HTTPS
     * @returns {Promise<Response>}
     */
    async fetchWithFallback(url) {
        try {
            // Thử HTTPS trước
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response;
        } catch (error) {
            // Nếu lỗi SSL hoặc network, thử HTTP fallback
            if (error.message.includes('CERT') || error.message.includes('Failed to fetch') || error.name === 'TypeError') {
                console.warn('HTTPS failed, trying HTTP fallback...', error.message);
                const httpUrl = url.replace('https://', 'http://');
                try {
                    const response = await fetch(httpUrl);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response;
                } catch (fallbackError) {
                    console.error('HTTP fallback also failed:', fallbackError);
                    throw new Error(`Cả HTTPS và HTTP đều thất bại: ${fallbackError.message}`);
                }
            }
            throw error;
        }
    }

    /**
     * Load danh sách tỉnh/thành phố
     * @returns {Promise<Array>} Danh sách provinces
     */
    async loadProvinces() {
        try {
            // Kiểm tra cache trước
            if (this.cache.provinces) {
                console.log('Using cached provinces');
                return this.cache.provinces;
            }

            console.log('Loading provinces from API...');
            const response = await this.fetchWithFallback(`${this.baseUrl}/?depth=1`);
            
            const data = await response.json();
            
            if (!Array.isArray(data) || data.length === 0) {
                throw new Error('Không có dữ liệu tỉnh/thành');
            }

            // Cache dữ liệu
            this.cache.provinces = data;
            
            console.log(`Loaded ${data.length} provinces successfully`);
            return data;
            
        } catch (error) {
            console.error('Lỗi load tỉnh/thành:', error);
            throw error;
        }
    }

    /**
     * Load danh sách phường/xã theo mã tỉnh
     * @param {string} provinceCode - Mã tỉnh/thành phố
     * @returns {Promise<Array>} Danh sách wards
     */
    async loadWards(provinceCode) {
        try {
            // Kiểm tra cache trước
            if (this.cache.wards.has(provinceCode)) {
                console.log(`Using cached wards for province: ${provinceCode}`);
                return this.cache.wards.get(provinceCode);
            }

            console.log(`Loading wards for province: ${provinceCode}`);
            const response = await this.fetchWithFallback(`${this.baseUrl}/p/${provinceCode}?depth=3`);
            
            const data = await response.json();
            
            // Collect all wards from all districts
            let allWards = [];
            if (data.districts && Array.isArray(data.districts)) {
                data.districts.forEach(district => {
                    if (district.wards && Array.isArray(district.wards)) {
                        district.wards.forEach(ward => {
                            allWards.push({
                                code: ward.code,
                                name: `${ward.name} (${district.name})`,
                                ward_name: ward.name,
                                district_name: district.name
                            });
                        });
                    }
                });
            }

            // Sort wards by name
            allWards.sort((a, b) => a.name.localeCompare(b.name));

            // Cache dữ liệu
            this.cache.wards.set(provinceCode, allWards);
            
            console.log(`Loaded ${allWards.length} wards for province: ${provinceCode}`);
            return allWards;
            
        } catch (error) {
            console.error(`Lỗi load phường/xã cho tỉnh ${provinceCode}:`, error);
            throw error;
        }
    }

    /**
     * Populate province select element
     * @param {HTMLElement} selectElement - Select element để populate
     * @param {string} placeholder - Placeholder text
     */
    async populateProvinceSelect(selectElement, placeholder = '-- Chọn tỉnh/thành --') {
        try {
            const provinces = await this.loadProvinces();
            
            // Clear existing options
            selectElement.innerHTML = `<option value="">${placeholder}</option>`;
            
            // Add provinces to select
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                option.dataset.name = province.name;
                selectElement.appendChild(option);
            });
            
        } catch (error) {
            selectElement.innerHTML = `<option value="">Lỗi tải dữ liệu</option>`;
            throw error;
        }
    }

    /**
     * Populate ward select element
     * @param {HTMLElement} selectElement - Select element để populate
     * @param {string} provinceCode - Mã tỉnh/thành phố
     * @param {string} placeholder - Placeholder text
     */
    async populateWardSelect(selectElement, provinceCode, placeholder = '-- Chọn phường/xã --') {
        try {
            if (!provinceCode) {
                selectElement.innerHTML = `<option value="">${placeholder}</option>`;
                return;
            }

            const wards = await this.loadWards(provinceCode);
            
            // Clear existing options
            selectElement.innerHTML = `<option value="">${placeholder}</option>`;
            
            // Add wards to select
            wards.forEach(ward => {
                const option = document.createElement('option');
                option.value = ward.code;
                option.textContent = ward.name;
                option.dataset.name = ward.ward_name;
                selectElement.appendChild(option);
            });
            
        } catch (error) {
            selectElement.innerHTML = `<option value="">Lỗi tải dữ liệu</option>`;
            throw error;
        }
    }

    /**
     * Clear cache (useful for testing or when data changes)
     */
    clearCache() {
        this.cache.provinces = null;
        this.cache.wards.clear();
        console.log('Province cache cleared');
    }

    /**
     * Get cached provinces (synchronous)
     * @returns {Array|null} Cached provinces or null
     */
    getCachedProvinces() {
        return this.cache.provinces;
    }

    /**
     * Get cached wards for province (synchronous)
     * @param {string} provinceCode - Mã tỉnh/thành phố
     * @returns {Array|null} Cached wards or null
     */
    getCachedWards(provinceCode) {
        return this.cache.wards.get(provinceCode) || null;
    }
}

// Export for use in different environments
if (typeof module !== 'undefined' && module.exports) {
    // Node.js environment
    module.exports = ProvinceService;
} else if (typeof window !== 'undefined') {
    // Browser environment
    window.ProvinceService = ProvinceService;
}

// Auto-initialize global instance
if (typeof window !== 'undefined') {
    window.provinceService = new ProvinceService();
}
