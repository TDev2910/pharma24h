# Hướng Dẫn Sử Dụng Framework Prompt Phân Tích Dự Án

## 🚀 QUICK START GUIDE

### Bước 1: Chuẩn bị thông tin dự án
```bash
# Lấy cấu trúc thư mục
tree -I 'node_modules|vendor|storage' -a

# Hoặc dùng ls với depth limit
find . -type d -not -path '*/\.*' | head -20
```

### Bước 2: Xác định scope phân tích
- **Full Project Analysis**: Sử dụng prompt chính
- **Specific Component**: Sử dụng specialized prompts
- **Performance Focus**: Thêm performance-specific requirements

### Bước 3: Customize prompt theo dự án
- Thay thế [placeholders] với thông tin thực tế
- Thêm context về business requirements
- Specify team constraints và timelines

## 📋 CHECKLIST TRƯỚC KHI CHẠY PROMPT

### ✅ Thông tin cần chuẩn bị:
- [ ] Cấu trúc thư mục đầy đủ
- [ ] 2-3 file code representative nhất  
- [ ] Biết stack công nghệ chính xác
- [ ] Hiểu business context của dự án
- [ ] Xác định budget/timeline cho improvements

### ✅ Mục tiêu phân tích rõ ràng:
- [ ] Architecture review
- [ ] Performance optimization  
- [ ] Code quality improvement
- [ ] Security assessment
- [ ] Testing strategy
- [ ] Technical debt reduction

## 🎯 CÁC TÌNH HUỐNG SỬ DỤNG THƯỜNG GẶP

### 🔥 Tình huống 1: Dự án legacy cần modernization
**Prompt focus:**
```
Đặc biệt chú ý:
- Migration strategy từ old patterns
- Backward compatibility considerations  
- Risk assessment cho major changes
- Phased implementation approach
```

### ⚡ Tình huống 2: Performance issues trong production
**Prompt focus:**
```
Ưu tiên phân tích:
- Database query performance
- Memory usage patterns
- Caching opportunities
- Infrastructure bottlenecks
- Quick wins vs long-term fixes
```

### 🏗️ Tình huống 3: Chuẩn bị cho team mở rộng
**Prompt focus:**
```
Tập trung vào:
- Code consistency và standards
- Documentation completeness
- Onboarding friendly architecture
- Testing coverage cho new features
- Development workflow optimization
```

### 🛡️ Tình huống 4: Security audit preparation  
**Prompt focus:**
```
Security-first analysis:
- Input validation coverage
- Authentication/authorization flows
- Data encryption và protection
- Third-party dependency risks
- Compliance requirements (GDPR, etc.)
```

## 🔧 CUSTOMIZATION PATTERNS

### Pattern 1: Multi-layered Analysis
```
Phase 1: High-level architecture review
→ Identify major structural issues

Phase 2: Component-level deep dive  
→ Focus on problematic components from Phase 1

Phase 3: Implementation details
→ Specific code improvements với examples
```

### Pattern 2: Priority-based Approach
```
Critical (Fix now): Security, major performance issues
High (Fix soon): Architecture violations, maintainability  
Medium (Plan ahead): Code quality, optimization opportunities
Low (Nice to have): Style improvements, minor refactoring
```

### Pattern 3: Role-specific Analysis
```
For Developers: Code quality, patterns, testing
For Architects: System design, scalability, integration
For DevOps: Performance, deployment, monitoring  
For Managers: Timeline, resource needs, ROI estimation
```

## 📊 ĐÁNH GIÁ KẾT QUẢ PROMPT

### ✅ Prompt hiệu quả khi:
- Đưa ra được 5-10 vấn đề cụ thể với solutions
- Có code examples cho mọi suggestion
- Priority được phân loại rõ ràng
- Timeline và effort estimation realistic
- Business impact được quantify

### ❌ Prompt cần cải thiện khi:
- Chỉ đưa ra lời khuyên chung chung
- Thiếu code examples cụ thể
- Không có implementation roadmap
- Quá focus vào issues nhỏ, bỏ qua big picture
- Không consider project constraints

## 🎨 PROMPT VARIATIONS CHO CÁC LOẠI DỰ ÁN

### Laravel Web Application
```
Thêm vào prompt:
- Eloquent relationship optimization
- Blade component structure
- Route organization patterns
- Middleware usage evaluation
- Service provider architecture
```

### API-first Applications  
```
Thêm vào prompt:
- RESTful design compliance
- API versioning strategy
- Response format consistency
- Rate limiting implementation
- Documentation quality (OpenAPI)
```

### E-commerce Platforms
```
Thêm vào prompt:
- Shopping cart implementation
- Payment gateway integration
- Inventory management patterns
- Order processing workflow
- Customer data protection
```

### Multi-tenant SaaS
```
Thêm vào prompt:
- Tenant isolation strategies
- Database architecture (single vs multi-DB)
- Feature flag implementation
- Billing integration patterns
- Scalability considerations
```

## 🔍 ADVANCED PROMPT TECHNIQUES

### Technique 1: Iterative Refinement
```
Round 1: Broad analysis với generic prompt
Round 2: Deep dive vào top 3 issues identified
Round 3: Implementation details cho chosen solutions
```

### Technique 2: Comparative Analysis
```
Prompt yêu cầu so sánh:
- Current approach vs industry standards
- Multiple solution alternatives
- Cost-benefit analysis của improvements
- Before/after code examples
```

### Technique 3: Context-aware Prompting
```
Cung cấp rich context:
- Team experience level
- Business constraints
- Technical constraints  
- Timeline pressures
- Budget limitations
```

## 📈 MEASURING SUCCESS

### Key Metrics để track:
- **Code Quality Score**: Static analysis tools (SonarQube, CodeClimate)
- **Performance**: Response times, database query counts
- **Maintainability**: Cyclomatic complexity, code duplication
- **Security**: Vulnerability scan results
- **Test Coverage**: Unit/integration test percentages

### Before/After Comparison:
```
Chụp baseline trước khi implement:
- Performance benchmarks
- Code quality metrics  
- Security scan results
- Test coverage reports
- Developer productivity metrics
```

## 🎯 BEST PRACTICES

### ✅ DO:
- Always provide real code examples in prompts
- Be specific about project constraints
- Ask for quantified improvements when possible
- Request implementation timelines
- Include risk assessment for major changes

### ❌ DON'T:
- Use generic, one-size-fits-all prompts
- Ignore business context và constraints
- Ask for too many improvements at once
- Skip the prioritization step
- Forget to validate suggestions against project reality

## 🔄 CONTINUOUS IMPROVEMENT

### Monthly Reviews:
- Track implementation progress
- Measure actual vs expected improvements
- Refine prompt templates based on results
- Update technical focus areas

### Quarterly Audits:
- Complete re-analysis với updated prompts
- Compare current state vs previous analysis
- Identify new optimization opportunities
- Plan next improvement cycle