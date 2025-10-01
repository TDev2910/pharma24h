<template>
  <div>
    <!-- Header Component -->
    <Header :auth="auth" />
    
    <!-- Main Content -->
    <div class="container my-4 py-4">
      <!-- Tabs bộ lọc và sắp xếp -->
      <div class="d-flex align-items-center mb-4 filter-tabs">
        <div class="me-3">
          <button class="btn fw-medium me-3 px-0 ">Bộ lọc</button>
          <button class="btn fw-medium text-primary px-0 filter-tab" style="margin-left: 65px;">Thiết lập lại</button>
        </div>
        <div class="ms-auto d-flex align-items-center">
          <span class="me-2">Sắp xếp theo:</span>
          <div class="btn-group">
            <button class="btn border px-3 sort-btn">Giá giảm dần</button>
            <button class="btn border px-3 sort-btn">Giá tăng dần</button>
          </div>
        </div>
      </div>
      
      <div class="row">
        <!-- Bộ lọc bên trái -->
        <div class="col-lg-3 col-md-4">
          <!-- Khoảng giá -->
          <div class="mb-4">
            <h6 class="mb-3 fw-bold">Khoảng giá</h6>
            <div class="mb-2">
              <div class="input-group mb-2">
                <input type="number" class="form-control w-50" placeholder="Tối thiểu" style="max-width:180px;">
                <span class="input-group-text border-start-0">đ</span>
              </div>
              <div class="input-group mb-2">
                <input type="number" class="form-control w-50" placeholder="Tối đa" style="max-width:180px;">
                <span class="input-group-text border-start-0">đ</span>
              </div>
              <button class="btn btn-primary fw-bold" style="background-color:#005EB8; border:none; width: 215px;">
                Áp dụng
              </button>
            </div>

            <!-- Radio giá -->
            <div class="filter-group">
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price1">
                <label class="form-check-label" for="price1">Dưới 100.000 đ</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price2">
                <label class="form-check-label" for="price2">100.000 đ - 300.000 đ</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price3">
                <label class="form-check-label" for="price3">300.000 đ - 500.000 đ</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="price" id="price4">
                <label class="form-check-label" for="price4">Trên 500.000 đ</label>
              </div>
            </div>
          </div>
          <hr class="light-divider"style="width: 215px; background-color: grey;">
          <!-- Thương hiệu -->
          <div class="mb-4">
            <h6 class="mb-3 fw-bold">Thương hiệu</h6>
            <div class="mb-3">
              <input type="text" class="form-control mb-2" style="max-width:215px;" placeholder="Nhập tên thương hiệu">
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand1">s
              <label class="form-check-label" for="brand1">STELLA</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand2">
              <label class="form-check-label" for="brand2">DHG Pharma</label> 
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand3">
              <label class="form-check-label" for="brand3">Davipharm</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand4">
              <label class="form-check-label" for="brand4">Hasan- Demarpharm</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="brand5">
              <label class="form-check-label" for="brand5">Domesco</label>
            </div>
            <a href="#" class="text-primary">Xem thêm</a>
          </div>
        </div>

        <!-- Sản phẩm bên phải -->
        <div class="col-lg-9 col-md-8">
          <div class="row g-4">
            <!-- Sản phẩm 1 -->
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card product-card h-100 border-0">
                <img src="https://via.placeholder.com/150" class="card-img-top p-2" alt="Thytodux">
                <div class="card-body px-0 d-flex flex-column">
                  <h6 class="product-title mb-2">Dung dịch uống Thytodux 60mg/10ml Thymodulin</h6>
                  <div class="text-muted small mb-2">Hộp</div>
                  <div class="mt-auto">
                    <button class="btn btn-warning text-white w-100 rounded-1">Cần tư vấn dược sĩ</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Sản phẩm 2 -->
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card product-card h-100 border-0">
                <img src="https://via.placeholder.com/150" class="card-img-top p-2" alt="Focgo">
                <div class="card-body px-0 d-flex flex-column">
                  <h6 class="product-title mb-2">Viên nén Focgo 8mg Phong Phú kháng viêm giảm đau</h6>
                  <div class="text-muted small mb-2">Hộp</div>
                  <div class="mt-auto">
                    <button class="btn btn-warning text-white w-100 rounded-1">Cần tư vấn dược sĩ</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Sản phẩm 3 -->
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card product-card h-100 border-0">
                <img src="https://via.placeholder.com/150" class="card-img-top p-2" alt="Spulit">
                <div class="card-body px-0 d-flex flex-column">
                  <h6 class="product-title mb-2">Viên nang Spulit 100mg Slavia điều trị các bệnh về đường hô hấp</h6>
                  <div class="text-muted small mb-2">Hộp</div>
                  <div class="mt-auto">
                    <button class="btn btn-warning text-white w-100 rounded-1">Cần tư vấn dược sĩ</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Sản phẩm 4 -->
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card product-card h-100 border-0">
                <img src="https://via.placeholder.com/150" class="card-img-top p-2" alt="Rotundin">
                <div class="card-body px-0 d-flex flex-column">
                  <h6 class="product-title mb-2">Viên nén Rotundin 30mg Khánh Hòa hỗ trợ điều trị mất ngủ</h6>
                  <div class="text-muted small mb-2">Hộp</div>
                  <div class="mt-auto">
                    <button class="btn btn-warning text-white w-100 rounded-1">Cần tư vấn dược sĩ</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Sản phẩm 5 -->
            <div class="col-lg-3 col-md-4 col-sm-6">
              <div class="card product-card h-100 border-0">
                <img src="https://via.placeholder.com/150" class="card-img-top p-2" alt="Dasbrain">
                <div class="card-body px-0 d-flex flex-column">
                  <h6 class="product-title mb-2">Viên nang Dasbrain điều trị tăng cholesterol máu</h6>
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Chai</span>
                    <span class="text-primary fw-bold">399.000 đ/Chai</span>
                  </div>
                  <div class="mt-auto">
                    <button class="btn btn-primary w-100 rounded-1">Chọn sản phẩm</button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Thêm các sản phẩm còn lại tương tự -->
          </div>
        </div>
      </div>
    </div>
    
    <!-- Footer Component -->
   <Footer/>
  </div>
</template>

<script setup>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'

const props = defineProps({
  auth: { type: Object, default: () => ({ user: null }) }
})
</script>

<style scoped>
.filter-group
{
  margin-top: 15px;
}

.filter-tab {
  position: relative;
  font-weight: 500;
}

.filter-tab.active {
  font-weight: 600;
}

.filter-tab.active::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -3px;
  height: 2px;
  width: 100%;
  background-color: #0d6efd;
}

/* Nút sắp xếp */
.sort-btn {
  color: #666;
  background-color: #fff;
}

.sort-btn:hover {
  background-color: #f8f9fa;
}

/* Card sản phẩm */
.product-card {
  transition: all 0.2s ease;
  box-shadow: none;
}

.product-card:hover {
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}

.product-title {
  font-size: 0.9rem;
  min-height: 40px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Nút "Cần tư vấn dược sĩ" */
.btn-warning {
  background-color: #FF9800;
  border-color: #FF9800;
  font-size: 0.9rem;
}

.btn-warning:hover {
  background-color: #F57C00;
  border-color: #F57C00;
}

/* Nút "Chọn sản phẩm" */
.btn-primary {
  background-color: #1a56db;
  border-color: #1a56db;
  font-size: 0.9rem;
}

.btn-primary:hover {
  background-color: #1650cf;
  border-color: #1650cf;
}

/* Responsive */
@media (max-width: 767px) {
  .filter-tabs {
    flex-direction: column;
    align-items: flex-start !important;
  }
  
  .filter-tabs > div.ms-auto {
    margin-top: 10px;
    margin-left: 0 !important;
  }
  
  .product-title {
    font-size: 0.85rem;
  }
}
</style>