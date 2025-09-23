// Rich-Text Editor (Summernote) cho mô tả thuốc/hàng hóa
(function(){
  const instances = new WeakMap();

  // Chờ jQuery + Summernote sẵn sàng rồi mới chạy
  function whenReady(cb){
    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.summernote) return cb();
    setTimeout(function(){ whenReady(cb); }, 50);
  }

  // Xây toolbar mặc định cho Summernote (có thể mở rộng qua data-rte-toolbar)
  function buildToolbar(preset){
    // Có thể mở rộng qua data-rte-toolbar về sau
    return [
      ['style', ['style']],
      ['font', ['bold', 'italic', 'underline', 'clear']],
      ['fontname', ['fontname']],
      ['color', ['color']],
      ['para', ['ul', 'ol', 'paragraph']],
      // Dùng nút mặc định picture để mở hộp chọn file
      ['insert', ['picture', 'link', 'video']],
      ['view', ['codeview', 'help']]
    ];
  }

  // Làm sạch HTML: loại bỏ style/class/data-* và chỉ giữ thẻ/thuộc tính cho phép
  function cleanHtml(dirtyHtml){
    try{
      var parser = new DOMParser();
      var doc = parser.parseFromString(dirtyHtml, 'text/html');
  
      // Thẻ cho phép
      var allowedTags = new Set([
        'P','B','I','U','BR','UL','OL','LI','A','IMG','STRONG','EM','SPAN',
        'H1','H2','H3','H4','H5','H6','TABLE','TBODY','TR','TD','TH'
      ]);
  
      // Thuộc tính cho phép theo thẻ
      var allowedAttrs = {
        A: new Set(['href','title','target','rel']),
        IMG: new Set(['src','alt','title']),
        // Cho phép style ở các thẻ text để giữ màu
        SPAN: new Set(['style']),
        P:    new Set(['style']),
        H1:   new Set(['style']), H2:new Set(['style']), H3:new Set(['style']),
        H4:   new Set(['style']), H5:new Set(['style']), H6:new Set(['style']),
        LI:   new Set(['style']), TD:new Set(['style']), TH:new Set(['style'])
      };
  
      // Thuộc tính style cho phép (whitelist)
      var allowedStyleProps = new Set(['text-align','color','background-color']);
  
      (function walk(node){
        Array.from(node.children).forEach(function(el){
          // Loại thẻ không cho
          if (!allowedTags.has(el.tagName)) {
            while (el.firstChild) el.parentNode.insertBefore(el.firstChild, el);
            el.remove();
            return;
          }
  
          // Lọc attributes
          Array.from(el.attributes).forEach(function(attr){
            var allow = allowedAttrs[el.tagName] || new Set();
            if (!allow.has(attr.name)) el.removeAttribute(attr.name);
          });
  
          // Lọc style: chỉ giữ text-align, color, background-color
          if (el.hasAttribute('style')) {
            var style = el.getAttribute('style');
            var kept = [];
            style.split(';').forEach(function(rule){
              var kv = rule.split(':');
              if (kv.length < 2) return;
              var k = kv[0].trim().toLowerCase();
              var v = kv.slice(1).join(':').trim();
              if (allowedStyleProps.has(k)) kept.push(k + ':' + v);
            });
            if (kept.length) el.setAttribute('style', kept.join('; '));
            else el.removeAttribute('style');
          }
  
          // Xóa class và data-*
          el.removeAttribute('class');
          Array.from(el.attributes).forEach(function(attr){
            if (attr.name.startsWith('data-')) el.removeAttribute(attr.name);
          });
  
          walk(el);
        });
      })(doc.body);
  
      return doc.body.innerHTML;
    }catch(_){
      return dirtyHtml;
    }
  }
  
  
  // Khởi tạo Summernote cho 1 textarea
  function initOne(textarea){
    if (!textarea || instances.get(textarea)) return;

    var $ = window.jQuery;
    var height = parseInt(textarea.getAttribute('data-rte-height') || '300', 10);
    var placeholder = textarea.getAttribute('placeholder') || 'Nhập nội dung...';
    var uploadUrl = textarea.getAttribute('data-upload-url');
    var csrf = (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || '';

    var options = {
      height: height,
      placeholder: placeholder,
      toolbar: buildToolbar(textarea.getAttribute('data-rte-toolbar')),
      callbacks: {},
      dialogsInBody: true
    };

    if (uploadUrl) {
      options.callbacks.onImageUpload = function(files){
        if (!files || !files.length) return;
        var data = new FormData();
        data.append('file', files[0]);
        if (csrf) data.append('_token', csrf);

        fetch(uploadUrl, { method: 'POST', body: data })
          .then(function(r){ return r.json(); })
          .then(function(payload){
            if (payload && payload.url) {
              $(textarea).summernote('insertImage', payload.url, function($image){
                try {
                  $image.addClass('img-fluid');
                  $image.css({ maxWidth: '100%', height: 'auto', width: '480px' });
                } catch(_){}}
              );
            } else {
              alert('Tải ảnh thất bại');
            }
          })
          .catch(function(){ alert('Tải ảnh thất bại'); });
      };
    }

    // Làm sạch nội dung khi DÁN từ clipboard (ngăn inline style/class từ nguồn ngoài)
    options.callbacks.onPaste = function(e){
      var ev = e.originalEvent || e;
      var clip = ev.clipboardData || window.clipboardData;
      if (clip) {
        var html = clip.getData('text/html') || clip.getData('text');
        if (html) {
          e.preventDefault();
          $(textarea).summernote('pasteHTML', cleanHtml(html));
        }
      }
    };

    // Làm sạch mỗi lần nội dung thay đổi (phòng người dùng sửa ở Code view)
    options.callbacks.onChange = function(contents){
      var cleaned = cleanHtml(contents || '');
      if (cleaned !== contents) {
        $(textarea).summernote('code', cleaned);
      }
    };

    $(textarea).summernote(options);

    // FIX dropdown Summernote khi dùng Bootstrap 5 (Summernote bs4)
    try {
      var editorRoot = $(textarea).next('.note-editor');
      var toolbarEl = editorRoot && editorRoot[0] ? editorRoot[0].querySelector('.note-toolbar') : null;
      if (toolbarEl && window.bootstrap && window.bootstrap.Dropdown) {
        // Đổi attribute và thêm class cần thiết
        toolbarEl.querySelectorAll('[data-toggle="dropdown"]').forEach(function(btn){
          btn.setAttribute('data-bs-toggle','dropdown');
          btn.classList.add('dropdown-toggle');
        });
        // Khởi tạo Dropdown cho tất cả nút có data-bs-toggle
        toolbarEl.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(btn){
          try { new window.bootstrap.Dropdown(btn, { boundary: 'viewport' }); } catch(_) {}
        });
      }
    } catch(_) {}

    instances.set(textarea, true);
  }

  // Khởi tạo editor cho tất cả textarea trong scope (toàn trang, modal hoặc tab mới mở)
  function initScope(root){
    var scope = root || document;
    scope.querySelectorAll('textarea[data-editor="summernote"], textarea[data-rte="true"], textarea.js-summernote, textarea#summernote').forEach(initOne);
  }

  // Tự khởi tạo khi trang load và khi modal hiển thị
  window.addEventListener('load', function(){ whenReady(function(){ initScope(document); }); });
  document.addEventListener('shown.bs.modal', function(e){ whenReady(function(){ initScope(e.target); }); });
  // Tùy biến hộp thoại chèn ảnh của Summernote: ẩn input URL, đổi text nút
  function tweakImageModal(modal){
    try {
      if (!modal) return;
      // Hide Image URL input row/group if present
      var urlInput = modal.querySelector('.note-image-url, input.note-image-url, .note-group-image-url input[type="text"], input[type="text"].note-form-control');
      if (urlInput) {
        var grp = urlInput.closest('.form-group, .note-form-group, .note-group, .note-input, .form-group.note-group-image-url');
        if (grp) grp.style.display = 'none'; else urlInput.style.display = 'none';
      }
      // Localize primary button to Vietnamese
      var primaryBtn = modal.querySelector('.note-btn-primary, button[type="submit"].note-btn');
      if (primaryBtn) primaryBtn.textContent = 'Tải ảnh lên';
    } catch(_) {}
  }

  document.addEventListener('shown.bs.modal', function(e){
    var modal = e.target;
    if (modal && modal.classList && modal.classList.contains('note-modal')) {
      tweakImageModal(modal);
    }
  });

  // Theo dõi DOM để bắt các dialog Summernote được thêm sau này
  var observer = new MutationObserver(function(mutations){
    mutations.forEach(function(m){
      m.addedNodes && m.addedNodes.forEach(function(node){
        if (node && node.classList && node.classList.contains('note-modal')) {
          tweakImageModal(node);
        }
      });
    });
  });
  try { observer.observe(document.body, { childList: true, subtree: true }); } catch(_) {}
  document.addEventListener('shown.bs.tab', function(e){
    var sel = e.target && e.target.getAttribute('data-bs-target');
    if (sel) { var pane = document.querySelector(sel); if (pane) whenReady(function(){ initScope(pane); }); }
  });

  // Inject CSS đảm bảo ảnh trong editor luôn gọn gàng
  (function(){
    var css = '.note-editor .note-editable img{max-width:100%;height:auto;}';
    var style = document.createElement('style');
    style.type = 'text/css';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  })();

  // Làm sạch nội dung lần cuối trước khi submit form (an toàn phía client)
  document.addEventListener('submit', function(e){
    var form = e.target;
    if (!form || !form.querySelector) return;
    (form.querySelectorAll('textarea[data-editor="summernote"], textarea[data-rte="true"]') || []).forEach(function(t){
      try{
        var $ = window.jQuery;
        var code = $ && $.fn && $(t).summernote ? $(t).summernote('code') : t.value;
        t.value = cleanHtml(code || '');
      }catch(_){ }
    });
  }, true);

  window.SharedRTE = {
    // API dùng chung để ép khởi tạo editor trong một scope cụ thể
    init: function(scope){ whenReady(function(){ initScope(scope||document); }); }
  };
})();