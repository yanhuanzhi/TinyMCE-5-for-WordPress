<?php
/*
Plugin Name: TinyMCE 5 for WordPress
Plugin URI: https://www.kuetr.cn/
Description: 用 TinyMCE 5 替换 WordPress 默认的古腾堡编辑器和经典编辑器。
Version: 1.0.0
Author: Horace Lee
Author URI: https://www.kuetr.cn/
License: GPL2
*/

// 禁用古腾堡编辑器
add_filter('use_block_editor_for_post', '__return_false');
add_filter('use_block_editor_for_post_type', '__return_false');

// 禁用经典编辑器
add_action('admin_init', function () {
    remove_post_type_support('post', 'editor');
    remove_post_type_support('page', 'editor');
});

// 加载 TinyMCE 脚本和样式
add_action('admin_enqueue_scripts', function () {
    global $pagenow;

    // 只在文章编辑和新建页面加载TinyMCE
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return;
    }

    $tinymce_path = plugins_url('tinymce', __FILE__);
    wp_enqueue_script('tinymce', $tinymce_path . '/tinymce.min.js', array(), null, true);

    // 本地化脚本变量
    wp_enqueue_script('tinymce-init', plugins_url('tinymce-init.js', __FILE__), array('tinymce', 'jquery'), null, true);
    wp_localize_script('tinymce-init', 'tinymce_upload_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tinymce_upload_nonce')
    ));
});

// 创建 TinyMCE 编辑器
add_action('edit_form_after_title', function () {
    global $post, $pagenow;

    // 检查是否在快速草稿区域
    if (
        isset($_GET['post_type']) && $_GET['post_type'] === 'post' &&
        isset($_GET['page']) && $_GET['page'] === 'quick-draft'
    ) {
        return;
    }

    // 只在文章编辑和新建页面显示编辑器
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return;
    }

    echo '<textarea id="content" name="content" style="width: 100%; height: 500px;">' . esc_textarea($post->post_content) . '</textarea>';
});

// 创建初始化脚本文件
add_action('init', function () {
    $init_script_path = plugin_dir_path(__FILE__) . 'tinymce-init.js';

    if (!file_exists($init_script_path)) {
        $init_script_content = "tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink link image lists charmap print preview',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
        });";
        file_put_contents($init_script_path, $init_script_content);
    }
});

// 添加TinyMCE图片上传功能
add_action('wp_ajax_tinymce_upload_image', 'handle_tinymce_image_upload');

function handle_tinymce_image_upload()
{
    // 验证nonce
    check_ajax_referer('tinymce_upload_nonce', 'security');

    // 检查用户权限
    if (!current_user_can('upload_files')) {
        wp_send_json_error(array('message' => '您没有上传文件的权限'), 403);
    }

    // 检查文件上传
    if (empty($_FILES['file'])) {
        wp_send_json_error(array('message' => '没有上传文件'), 400);
    }

    // 文件类型检查
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        wp_send_json_error(array('message' => '只允许上传JPG, PNG或GIF图片'), 400);
    }

    // 文件大小限制(2MB)
    if ($_FILES['file']['size'] > 2 * 1024 * 1024) {
        wp_send_json_error(array('message' => '图片大小不能超过2MB'), 400);
    }

    // 使用WordPress媒体处理上传
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $upload = wp_handle_upload($_FILES['file'], array('test_form' => false));

    if (isset($upload['error'])) {
        wp_send_json_error(array('message' => $upload['error']), 500);
    }

    // 创建附件
    $attachment_id = wp_insert_attachment(array(
        'post_mime_type' => $upload['type'],
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($upload['file'])),
        'post_content' => '',
        'post_status' => 'inherit'
    ), $upload['file']);

    // 生成附件元数据
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    // 返回成功响应
    wp_send_json_success(array(
        'location' => $upload['url']
    ));
}
