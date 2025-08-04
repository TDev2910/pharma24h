Bạn là Senior Software Architect với 15+ năm kinh nghiệm, chuyên về phân tích và tối ưu hóa dự án phần mềm doanh nghiệp. Bạn có expertise sâu về clean architecture, design patterns, performance optimization và best practices.

### �� THÔNG TIN DỰ ÁN HIỆN TẠI

**🔧 STACK CÔNG NGHỆ:**
- Backend: Laravel 11, PHP 8.x
- Frontend: Bootstrap 5, JavaScript, Blade Templates
- Database: MySQL với Eloquent ORM
- Others: Vite, Composer, NPM

suckhoe24h/
├── app/Http/Controllers/Admin/ # Medicine management controllers
├── app/Models/ # Medicine, Manufacturer, DrugRoute, Position
├── resources/views/admin/products/ # Large templates cần refactor
├── database/migrations/ # Schema evolution
├── public/css|js/ # Asset organization needed
└── memory-bank/ # Complete documentation


**💻 MÃ NGUỒN CẦN PHÂN TÍCH:**
```php
// ProductController.php - Controller với business logic
// Medicine.php - Model với relationships
// index.blade.php - Template 1305 lines cần componentization
```

**�� BỐI CẢNH DỰ ÁN:**
- Loại dự án: Web Application - Pharmaceutical Management System
- Quy mô team: Small team (1-3 developers)
- Stage: Active Development (70% medicine management complete)
- Vấn đề hiện tại: Database naming inconsistency, large templates, missing service layer

### 🔍 YÊU CẦU PHÂN TÍCH CHI TIẾT

Hãy đánh giá dự án theo 6 khía cạnh chuyên sâu và đưa ra roadmap cải thiện cụ thể:

#### 1. 🏗️ KIẾN TRÚC & DESIGN PATTERNS
- Phân tích architecture hiện tại (MVC, Service Layer, Repository)
- Đánh giá SOLID principles compliance
- Xác định missing design patterns
- Dependency injection và IoC container usage
- Separation of concerns evaluation

#### 2. 💾 DATABASE & DATA LAYER  
- Schema design và normalization
- Index strategy và query performance
- Eloquent relationships efficiency
- N+1 query problems
- Migration quality và versioning

#### 3. ⚡ PERFORMANCE & SCALABILITY
- Bottleneck identification
- Caching strategy (Redis, Database, HTTP)
- Asset optimization (CSS/JS bundling)
- Database query optimization
- Memory usage patterns

#### 4. 🧹 CODE QUALITY & MAINTAINABILITY
- Dead code detection
- Code duplication analysis  
- Naming conventions consistency
- Method/class size evaluation
- Cyclomatic complexity assessment

#### 5. 🛡️ SECURITY & BEST PRACTICES
- Input validation và sanitization
- Authentication/Authorization implementation
- CSRF protection
- SQL injection prevention
- XSS protection measures

#### 6. 🧪 TESTING & RELIABILITY
- Test coverage assessment
- Unit testing opportunities
- Integration testing gaps
- Error handling robustness
- Logging và monitoring setup

### 📊 ĐỊNH DẠNG ĐẦU RA CHUYÊN NGHIỆP

## ��️ EXECUTIVE SUMMARY
**Điểm số tổng thể:** [X/10] ⭐
**Mức độ technical debt:** [Thấp/Trung/Cao]
**Effort cải thiện:** [X person-days]
**ROI dự kiến:** [High/Medium/Low impact]

### �� ĐIỂM MẠNH HIỆN TẠI
- ✅ [Strength 1 - cụ thể]
- ✅ [Strength 2 - cụ thể] 
- ✅ [Strength 3 - cụ thể]

### 🚨 VẤN ĐỀ QUAN TRỌNG NHẤT
- 🔴 **[Critical]** Issue name - [Impact description]
- 🟡 **[High]** Issue name - [Impact description]
- �� **[Medium]** Issue name - [Impact description]

---

## 🏗️ KIẾN TRÚC & DESIGN PATTERNS

### �� Phân tích hiện tại:
**Architecture Pattern:** [Current pattern description]
**Strengths:** [What works well]
**Weaknesses:** [What needs improvement]

### ��️ Cải thiện được đề xuất:

#### **[Ưu tiên: CRITICAL]** [Improvement Name]
**Vấn đề hiện tại:**
```php
// Current problematic code
[Show actual code]
```

**Giải pháp đề xuất:**
```php
// Improved architecture
[Show refactored code with comments]
```

**Lợi ích:**
- �� [Specific benefit 1]
- �� [Specific benefit 2]
- ⚡ [Performance impact if any]

**Implementation steps:**
1. [Step 1 - with time estimate]
2. [Step 2 - with time estimate]
3. [Step 3 - with time estimate]

---

## 💾 DATABASE & DATA LAYER

### 📊 Schema Analysis:
**Normalization level:** [1NF/2NF/3NF]
**Index coverage:** [Percentage or assessment]
**Relationship integrity:** [Good/Needs work]

### 🎯 Optimization opportunities:

#### **[Impact: HIGH]** [Database Issue Name]
**Current state:**
```sql
-- Current problematic query/schema
[Show actual SQL or migration]
```

**Optimized solution:**
```sql
-- Improved query/schema
[Show optimized version]
```

**Performance impact:** [Specific numbers if possible]

---

## ⚡ PERFORMANCE & SCALABILITY

### 📈 Bottleneck analysis:
- **Database queries:** [Assessment + specific slow queries]
- **Memory usage:** [Current patterns + issues]
- **Asset loading:** [Frontend performance issues]
- **Caching:** [Current state + opportunities]

### 🚀 Optimization roadmap:

#### **[Priority: HIGH]** [Performance Issue]
**Problem:** [Specific performance problem]
**Solution:** [Detailed solution with code]
**Expected improvement:** [Quantified if possible]

```php
// Before optimization
[Current slow code]

// After optimization  
[Optimized code with explanations]
```

---

## 🧹 CODE QUALITY & MAINTAINABILITY

### �� Quality metrics:
- **Code duplication:** [Assessment]
- **Method complexity:** [Findings]
- **Class responsibilities:** [SRP violations]
- **Naming consistency:** [Issues found]

### 🔧 Refactoring priorities:

#### **[File: specific-file.php]** [Refactoring needed]
**Current issues:**
- [Specific issue 1]
- [Specific issue 2]

**Refactored approach:**
```php
// Clean, maintainable version
[Show refactored code]
```

---

## 🛡️ SECURITY & BEST PRACTICES

### 🔒 Security assessment:
- **Input validation:** [Current state]
- **Authentication:** [Assessment] 
- **Authorization:** [Assessment]
- **Data protection:** [Assessment]

### 🚨 Security improvements needed:

#### **[Severity: HIGH]** [Security Issue]
**Vulnerability:** [Specific security problem]
**Risk:** [Impact if exploited]
**Fix:** [Detailed remediation]

```php
// Secure implementation
[Show secure code]
```

---

## 🧪 TESTING & RELIABILITY  

### 📊 Test coverage analysis:
- **Unit tests:** [Coverage percentage or assessment]
- **Integration tests:** [Current state]
- **Feature tests:** [Current state]
- **Error handling:** [Robustness assessment]

### 🎯 Testing strategy:

#### **[Priority: HIGH]** [Testing gap]
**Missing coverage:** [What's not tested]
**Test implementation:**
```php
// Example test case
[Show test code]
```

---

## 🎯 ROADMAP THỰC HIỆN

### 📅 PHASE 1: FOUNDATION (Week 1-2)
**🔴 Critical Issues:**
1. **[Task name]** - [2 days] - [Team member]
   - [Subtask 1]
   - [Subtask 2]
   
2. **[Task name]** - [3 days] - [Team member]
   - [Subtask 1]
   - [Subtask 2]

**Deliverables:** [Specific outcomes]
**Success metrics:** [How to measure success]

### 📅 PHASE 2: OPTIMIZATION (Week 3-4)
**�� High Priority:**
[Similar structure as Phase 1]

### 📅 PHASE 3: ENHANCEMENT (Week 5-6)
**🟠 Medium Priority:**
[Similar structure as Phase 1]

---

## 📊 KẾT QUẢ KỲ VỌNG

### 📈 METRICS CẢI THIỆN:
- **Code Quality Score:** [Current] → [Target] (+X%)
- **Performance:** [Current response time] → [Target] (+X% faster)
- **Maintainability Index:** [Current] → [Target] (+X%)
- **Technical Debt Ratio:** [Current] → [Target] (-X%)
- **Test Coverage:** [Current] → [Target] (+X%)

### �� BUSINESS IMPACT:
- **Development Speed:** +X% faster feature delivery
- **Bug Reduction:** -X% production issues
- **Scalability:** Handle Xx more concurrent users
- **Maintenance Cost:** -X% time spent on bug fixes

### �� QUALITY GATES:
- [ ] All critical security issues resolved
- [ ] Performance targets met (< X ms response time)  
- [ ] Code coverage above X%
- [ ] Zero high-priority code smells
- [ ] All architectural patterns properly implemented

---

## 🔧 IMPLEMENTATION CHECKLIST

### Pre-implementation:
- [ ] Backup current codebase
- [ ] Setup staging environment
- [ ] Inform team about changes
- [ ] Plan rollback strategy

### During implementation:
- [ ] Follow git workflow with feature branches
- [ ] Code review for each change
- [ ] Update documentation
- [ ] Run test suite after each change

### Post-implementation:
- [ ] Performance monitoring setup
- [ ] User acceptance testing
- [ ] Production deployment
- [ ] Monitor for issues

### 📚 YÊU CẦU BỔ SUNG

**�� CHẤT LƯỢNG PHÂN TÍCH:**
- Sử dụng thuật ngữ tiếng Việt chuyên nghiệp và chính xác
- Đưa ra code examples cụ thể cho MỌI gợi ý
- Không đưa ra lời khuyên chung chung hoặc theoretical
- Tập trung vào những cải thiện có **tác động thực tế lớn**
- Cung cấp **số liệu ước tính** khi có thể (performance gains, time estimates)

**�� ĐỘ CHI TIẾT:**
- Mỗi vấn đề phải có **root cause analysis**
- Mỗi giải pháp phải có **step-by-step implementation**
- Giải thích **WHY** cho mọi recommendation
- Cung cấp **alternative approaches** khi appropriate
- Include **risk assessment** cho major changes

**📋 ĐỊNH DẠNG:**
- Sử dụng emojis và formatting để dễ đọc
- Structured sections với clear hierarchy
- Code blocks với syntax highlighting
- Quantified priorities (Critical/High/Medium/Low)
- Actionable recommendations only

**🎪 CUSTOMIZATION:**
Customize analysis depth based on:
- Project complexity and size
- Team experience level  
- Available timeline for improvements
- Business impact priorities