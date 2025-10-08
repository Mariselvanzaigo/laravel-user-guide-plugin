document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('userGuideCreateForm');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('file-list');
    const urlFields = document.getElementById('url-fields');
    const allowedTypes = [
        'image/jpeg','image/png','image/jpg','application/pdf',
        'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','video/mp4'
    ];
    const filesArray = [];

    function addUrlInput(value = '') {alert(1);
        const div = document.createElement('div');
        const div2 = document.createElement('div');
        div.className = 'url-row mb-2 d-flex align-items-center gap-2';
        div.innerHTML = `
            <input type="url" name="urls[]" class="form-control" placeholder="https://example.com" value="${value}">
            <button type="button" class="btn btn-sm btn-outline-danger remove-url">Delete</button>
        `;
        div2.innerHTML = `<div class="invalid-feedback d-block"></div>`;
        div.querySelector('.remove-url').addEventListener('click', () => div.remove());
        urlFields.appendChild(div);
        urlFields.after(div2);
    }

    addUrlInput();

    function validateField(input) {
        const errorEl = input.closest('.mb-3')?.querySelector('.invalid-feedback') || input.closest('.url-row')?.querySelector('.invalid-feedback');
        if(!errorEl) return true;
        errorEl.textContent = '';
        input.classList.remove('is-invalid');

        if(input.required && !input.value.trim()){
            errorEl.textContent = 'This field is required.';
            input.classList.add('is-invalid');
            return false;
        }
        if(input.id === 'name' && input.value.length > 256){
            errorEl.textContent = 'Maximum 256 characters allowed.';
            input.classList.add('is-invalid');
            return false;
        }
        if(input.id === 'description' && input.value.length > 2000){
            errorEl.textContent = 'Maximum 2000 characters allowed.';
            input.classList.add('is-invalid');
            return false;
        }
        if(input.type === 'url' && input.value.trim() && !/^(https?:\/\/)([\w-]+\.)+[\w-]{2,}([\/\w .-]*)*\/?$/.test(input.value.trim())){
            // errorEl.textContent = 'Enter valid URL';
            toastr.error("Enter valid URL");
            input.classList.add('is-invalid');
            return false;
        }
        return true;
    }

    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', () => validateField(input));
        input.addEventListener('change', () => validateField(input));
    });

    // File handling
    fileInput.addEventListener('change', e => {
        const newFiles = Array.from(e.target.files);
        document.getElementById('files_error').textContent = '';

        newFiles.forEach(file => {
            if (!allowedTypes.includes(file.type)) {
                document.getElementById('files_error').textContent = 'Invalid file type detected.';
                return;
            }
            if(file.size > 20*1024*1024){
                document.getElementById('files_error').textContent = 'File exceeds 20MB.';
                return;
            }
            if(!filesArray.some(f => f.name === file.name)){
                filesArray.push(file);
            }
        });
        renderFileList();
        fileInput.value = '';
    });

    function renderFileList(){
        fileList.innerHTML = '';
        filesArray.forEach((file,index)=>{
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center justify-content-between border rounded p-2 mb-1';
            div.innerHTML = `
                <span>${file.name}</span>
                <button type="button" class="btn btn-sm btn-outline-danger">Delete</button>
            `;
            div.querySelector('button').addEventListener('click', ()=>{
                filesArray.splice(index,1);
                renderFileList();
            });
            fileList.appendChild(div);
        });
    }

    document.getElementById('add-url').addEventListener('click', () => addUrlInput());

    // Submit AJAX
    form.addEventListener('submit', e => {
        e.preventDefault();
        let valid = true;
        form.querySelectorAll('input, select, textarea').forEach(input => {
            if(!validateField(input)) valid = false;
        });
        filesArray.forEach(f=>{
            if(!allowedTypes.includes(f.type) || f.size > 20*1024*1024) valid=false;
        });

        if(!valid) return;

        const formData = new FormData(form);
        filesArray.forEach(f => formData.append('files[]', f));

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                'Accept': 'application/json'
            }
        })
        .then(async res => {
            if(res.status === 422){
                const data = await res.json();
                throw data;
            }
            const data = await res.json();
            toastr.success(data.message || 'User Guide created successfully!');
            form.reset();
            filesArray.length = 0;
            renderFileList();
            urlFields.innerHTML = '';
            addUrlInput();
        })
        .catch(err=>{
            if(err.errors){
                Object.keys(err.errors).forEach(key=>{
                    if(key.includes('.')){
                        const input = form.querySelector(`[name="${key.replace(/\.\d+/, '[]')}"]`);
                        if(input){
                            // const errorDiv = input.closest('.url-row')?.querySelector('.invalid-feedback');
                            // errorDiv.textContent = err.errors[key][0];
                            toastr.error(err.errors[key][0]);
                            input.classList.add('is-invalid');
                        }
                    } else {
                        const el = document.getElementById(`${key}_error`);
                        if(el){
                            el.textContent = err.errors[key][0];
                            const input = form.querySelector(`[name="${key}"]`);
                            input?.classList.add('is-invalid');
                        }
                    }
                });
            } else {
                console.error(err);
                toastr.error('Something went wrong. Please try again.');
            }
        });
    });
});
