/* 
tinyMCE 配置文件

author:Horace Lee
website:https://www.kuetr.cn
tinyMCE 中文配置文档：http://tinymce.ax-z.cn/

*/

tinymce.init({
    selector: '#content',
    placeholder: '请在这里输入文字...',
    plugins: 'advlist autolink emoticons code autosave visualblocks link image media lists codesample charmap print preview indent2em help charmap quickbars hr pagebreak searchreplace table wordcount',
    toolbar: ['undo redo selectall restoredraft searchreplace | formatselect fontselect fontsizeselect removeformat | bold italic underline strikethrough subscript superscript forecolor backcolor | alignleft aligncenter alignright alignjustify | table',
        'styleselect blockquote  codesample hr pagebreak | lineheight bullist numlist outdent indent indent2em | link image media emoticons charmap code visualblocks help'],

    language: 'zh_CN',
    fullscreen_native: false, // 禁用原生全屏模式
    content_style: "img {max-width:100%;}",
    min_height: 500,  // 编辑器最小高度
    font_formats: '微软雅黑=Microsoft YaHei,Helvetica Neue,PingFang SC,sans-serif;苹果苹方=PingFang SC,Microsoft YaHei,sans-serif;宋体=simsun,serif;仿宋=FangSong,serif;楷体=KaiTi,serif;黑体=SimHei,sans-serif;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino',
    fontsize_formats: '12px 14px 15px 16px 18px 20px 24px 36px 48px 60px 72px 96px',
    lineheight_formats: '1 1.1 1.2 1.3 1.4 1.5 1.6 1.7 1.8 2 2.4',
    quickbars_image_toolbar: 'alignleft aligncenter alignright',
    quickbars_insert_toolbar: false,
    quickbars_selection_toolbar: 'bold italic underline | h2 h3 link blockquote',
    visual: false,

    // 设置默认字体为微软雅黑
    setup: function (editor) {
        editor.on('init', function (e) {
            this.getDoc().body.style.fontFamily = '微软雅黑';
        });
    },

    // 图片上传配置
    images_upload_url: ajaxurl + '?action=tinymce_upload_image',
    images_upload_base_path: '/wp-content/uploads/',
    /**
     * 启用图片上传时携带凭证
     * 设置为 true 时会在图片上传请求中包含 cookie 等凭证信息
     * 用于需要身份验证的图片上传场景
     * @type {boolean}
     */
    images_upload_credentials: true,
    images_upload_handler: function (blobInfo, success, failure) {
        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;
        xhr.open('POST', ajaxurl + '?action=tinymce_upload_image');

        xhr.onload = function () {
            if (xhr.status != 200) {
                failure('上传失败: ' + xhr.status);
                return;
            }

            var json = JSON.parse(xhr.responseText);
            if (!json || !json.success || !json.data.location) {
                failure(json.data.message || '无效的服务器响应');
                return;
            }

            success(json.data.location);
        };

        var formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());
        formData.append('security', tinymce_upload_vars.nonce);

        xhr.send(formData);
    }
});