// 全局AJAX错误处理
$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    console.error("AJAX请求失败:", settings.url, thrownError);
    alert("请求失败，请重试");
});

// 表单提交处理
$("form").submit(function(e) {
    const form = $(this);
    if (form.data('submitted')) {
        e.preventDefault();
        return;
    }
    
    form.data('submitted', true);
    form.find('button[type="submit"]').prop('disabled', true);
});

// 图片预览功能
$("#images").change(function() {
    const files = this.files;
    const previewContainer = $("#imagePreview");
    
    previewContainer.empty();
    
    if (files.length > 5) {
        alert("最多只能上传5张图片");
        $(this).val('');
        return;
    }
    
    for (let i = 0; i < Math.min(files.length, 5); i++) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = $('<img>').attr('src', e.target.result).addClass('img-thumbnail me-2 mb-2').css({
                'max-width': '100px',
                'max-height': '100px'
            });
            previewContainer.append(img);
        }
        
        reader.readAsDataURL(files[i]);
    }
});

// 价格输入验证
$("#price").on('input', function() {
    let value = $(this).val();
    if (value < 0) {
        $(this).val(0);
    }
});