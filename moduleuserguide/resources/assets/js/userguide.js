// userguide.js
(function () {
let descriptionEditor;
  document.addEventListener('DOMContentLoaded', () => {
    ClassicEditor
      .create(document.querySelector('#description'), {
          toolbar: [
              'heading', '|', 'bold', 'italic', 'link',
              'bulletedList', 'numberedList', 'blockQuote',
              'insertTable', 'imageUpload'
          ],
          ckfinder: {
              uploadUrl: window.ckEditorUploadUrl
          }
      })
      .then(editor => {
          descriptionEditor = editor;

          editor.editing.view.change(writer => {
              writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
          });

          editor.model.document.on('change:data', () => {
              const ta = document.querySelector('#description');
              ta.classList.remove('is-invalid');
              const errDiv = document.querySelector('#description_error');
              if (errDiv) errDiv.textContent = '';
          });
      })
      .catch(error => console.error(error));

    // Initialize Select2
    // $('#module_id').select2({
    //     placeholder: "Select Module",
    //     width: '100%'
    // }).on('change', function() {
    //   validateField(this);
    // });
    const form = document.querySelector('#userGuideCreateForm') || document.querySelector('#userGuideEditForm');
    if (!form) return;

    const fileInput = form.querySelector('#fileInput');
    const fileList = form.querySelector('#file-list');
    const existingContainer = form.querySelector('#existing-files');
    const urlFields = form.querySelector('#url-fields');
    const addUrlBtn = form.querySelector('#add-url');
    const allowedTypes = [
      'image/jpeg',
      'image/png',
      'image/jpg',
      'application/pdf',
      'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'video/mp4',
      'video/x-m4v',
      'application/octet-stream', // sometimes mp4 can appear as this
      'video/quicktime'
    ];


    let filesArray = [];
    let existingFiles = [];

    // ---------- Helper: Escape HTML ----------
    function escapeHtml(text) {
      return text.replace(/[&<>"']/g, (m) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
      })[m]);
    }

    // ---------- Helper: Format bytes ----------
    function formatBytes(bytes) {
      if (bytes === 0) return '0 B';
      const k = 1024;
      const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // ---------- Add URL input row ----------
    function addUrlInput(value = '') {
      const row = document.createElement('div');
      row.className = 'd-flex align-items-start gap-2 mb-2 url-row flex-wrap';
      row.innerHTML = `
        <div class="url-input-wrapper row m-0">
          <input type="url" name="urls[]" class="form-control w-75" placeholder="https://example.com" value="${value}">
          <button type="button" class="btn btn-sm btn-outline-danger remove-url" style="height: 38px;margin-left: 10px;">
            <i class="fa fa-trash"></i>
          </button>
        </div>
        <div class="url-error-wrapper" style="flex-basis: 50%; display: flex; align-items: center;">
          <div class="invalid-feedback d-block" style="display: none;"></div>
        </div>
      `;

      const btn = row.querySelector('.remove-url');
      btn.addEventListener('click', () => row.remove());

      const input = row.querySelector('input[name="urls[]"]');
      const errorBox = row.querySelector('.invalid-feedback');

      input.addEventListener('input', () => validateField(input, errorBox));
      input.addEventListener('change', () => validateField(input, errorBox));

      urlFields.appendChild(row);
      return row;
    }


    if (urlFields && urlFields.children.length === 0) addUrlInput();

    // ---------- Initialize existing files (edit) ----------
    if (existingContainer) {
      const nodes = existingContainer.querySelectorAll('[data-existing-file]');
      nodes.forEach(node => {
        const path = node.dataset.existingFile;
        if (path) existingFiles.push(path);
        node.classList.add('file-box');
      });
      attachExistingDeleteHandlers();
    }

    // ---------- Validate field ----------
    // function validateField(input) {
    window.validateField = function (input) {
    let errorEl = input.closest('.url-row')?.querySelector('.invalid-feedback') 
                  || input.closest('.mb-3')?.querySelector('.invalid-feedback');
    if (!errorEl) return true;

    errorEl.style.display = 'none';
    errorEl.textContent = '';
    input.classList.remove('is-invalid');

    // Select2 container fix
    if ($(input).hasClass('select2-hidden-accessible')) {
        $(input).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
    }

    // Required check
    if (input.required && !String(input.value || '').trim()) {
        errorEl.textContent = 'This field is required.';
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');

        // Add invalid class to Select2 container if applicable
        if ($(input).hasClass('select2-hidden-accessible')) {
            $(input).next('.select2-container').find('.select2-selection').addClass('is-invalid');
        }

        return false;
    }

    // Name max length
    if (input.id === 'name' && input.value.length > 256) {
        errorEl.textContent = 'Maximum 256 characters allowed.';
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
        return false;
    }

    // Description max length
    if (input.id === 'description' && input.value.length > 2000) {
        errorEl.textContent = 'Maximum 2000 characters allowed.';
        errorEl.style.display = 'block';
        input.classList.add('is-invalid');
        return false;
    }

    // URL strict regex
    if (input.name === 'urls[]' && input.value.trim()) {
        const urlRegex = /^(https?:\/\/)([\w-]+\.)+[\w-]{2,}([\/\w .-]*)*\/?$/;
        // const urlRegex = /^(https?:\/\/)?(www\.)?([\w-]+\.)+[\w-]{2,}([/?#][\w\-.,@?^=%&:\/~+#]*)?$/i;
        if (!urlRegex.test(input.value.trim())) {
            errorEl.textContent = 'Enter valid URL';
            errorEl.style.display = 'block';
            input.classList.add('is-invalid');
            return false;
        }
    }

    return true;
}


    // attach validation to all inputs
    form.querySelectorAll('input, select, textarea').forEach(input => {
      input.addEventListener('input', () => validateField(input));
      input.addEventListener('change', () => validateField(input));
    });
    $('.select2').on('change', function () {
         // 'this' is the original <select> element
    });
    // ---------- File input change ----------
    if (fileInput) {
      fileInput.addEventListener('change', (e) => {
        const newFiles = Array.from(e.target.files || []);
        document.getElementById('files_error').textContent = '';

        newFiles.forEach(file => {
          if (file.size > 20 * 1024 * 1024) {
            document.getElementById('files_error').textContent = 'File exceeds 20MB.';
            return;
          }
          if (!filesArray.some(f => f.name === file.name && f.size === file.size)) filesArray.push(file);
        });

        renderFileList();
        fileInput.value = '';
      });
    }

    // ---------- Render file list ----------
    function renderFileList() {
      if (!fileList) return;
      fileList.innerHTML = '';
      filesArray.forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'file-box';
        const ext = (file.name.split('.').pop() || '').substr(0, 3).toUpperCase();

        div.innerHTML = `
          <div class="file-meta">
            <div class="file-icon">${ext}</div>
            <div>
              <span class="file-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</span>
              <div class="file-size">${formatBytes(file.size)}</div>
            </div>
          </div>
          <div class="file-actions">
            <button type="button" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
          </div>
        `;

        div.querySelector('button').addEventListener('click', () => {
          filesArray.splice(index, 1);
          renderFileList();
        });

        fileList.appendChild(div);
      });
    }

    // ---------- Attach delete handlers for existing files ----------
    function attachExistingDeleteHandlers() {
      if (!existingContainer) return;
      existingContainer.querySelectorAll('.remove-existing-file').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const el = e.currentTarget.closest('[data-existing-file]');
          if (!el) return;
          const path = el.dataset.existingFile;

          if (form && path && !form.querySelector(`input[name="delete_files[]"][value="${path}"]`)) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'delete_files[]';
            hidden.value = path;
            form.appendChild(hidden);
          }

          el.remove();
          existingFiles = existingFiles.filter(p => p !== path);
        });
      });
    }

    // ---------- Normalize existing files markup ----------
    function normalizeExistingFilesMarkup() {
      if (!existingContainer) return;
      Array.from(existingContainer.children).forEach(child => {
        const path = child.dataset.existingFile;
        if (!path) return;
        const name = child.querySelector('a') ? child.querySelector('a').textContent.trim() : path.split('/').pop();
        const wrapper = document.createElement('div');
        wrapper.className = 'file-box';
        wrapper.dataset.existingFile = path;
        wrapper.innerHTML = `
          <div class="file-meta">
            <div class="file-icon">${(name.split('.').pop()||'').substr(0,3).toUpperCase()}</div>
            <div>
              <a href="${child.querySelector('a') ? child.querySelector('a').href : '#'}" target="_blank" class="file-name" title="${escapeHtml(name)}">${escapeHtml(name)}</a>
              <div class="file-size"></div>
            </div>
          </div>
          <div class="file-actions">
            <button type="button" class="btn btn-sm btn-danger remove-existing-file"><i class="fa fa-trash"></i></button>
          </div>
        `;
        existingContainer.replaceChild(wrapper, child);
      });
      attachExistingDeleteHandlers();
    }

    normalizeExistingFilesMarkup();

    // ---------- Add URL button ----------
    if (addUrlBtn) {
      addUrlBtn.addEventListener('click', () => addUrlInput());
    }

    // ---------- Form submit ----------
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      let valid = true;
      form.querySelectorAll('input, select, textarea').forEach(input => {
        if (!validateField(input)) valid = false;
      });

      if (!valid) return;

      const fd = new FormData(form);
      filesArray.forEach(f => fd.append('files[]', f));

      fetch(form.action, {
        method: form.method || 'POST',
        body: fd,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name=_token]')?.value || '',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(async res => {
        if (res.status === 422) throw await res.json();
        const data = await res.json();
        if (data.redirect) {
          sessionStorage.setItem('userGuideSuccess', data.message || 'Saved successfully!');
          window.location.href = data.redirect;
          return;
        }
        if (data.message) toastr.success(data.message);
        form.reset();
        filesArray = [];
        renderFileList();
        urlFields.innerHTML = '';
        addUrlInput();
      })
      .catch(err => {
        if (err.errors) {
          Object.keys(err.errors).forEach(key => {
            const input = form.querySelector(`[name="${key.replace(/\.\d+/, '[]')}"]`) || form.querySelector(`[name="${key}"]`);
            if (input) {
              const errorEl = input.closest('.url-row')?.querySelector('.invalid-feedback') || document.getElementById(`${key}_error`);
              if (errorEl) {
                errorEl.style.display = 'block';
                errorEl.textContent = err.errors[key][0];
              } else {
                toastr.error(err.errors[key][0]);
              }
            }
          });
          return;
        }
        toastr.error('Something went wrong. Try again.');
        console.error(err);
      });
    });

  });
})();
