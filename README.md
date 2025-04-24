# TinyMCE 5 for WordPress

一个强大的WordPress插件，用于将TinyMCE 5编辑器集成到WordPress中，替换默认的古腾堡编辑器和经典编辑器。

## 功能特性

- 完全替换WordPress默认的古腾堡编辑器
- 禁用经典编辑器，提供更现代的编辑体验
- 集成TinyMCE 5的强大功能
- 支持图片上传功能
- 提供多种编辑器插件支持
- 支持中文界面

## 安装说明

1. 下载插件压缩包
2. 将压缩包解压到WordPress的`wp-content/plugins/`目录
3. 在WordPress管理后台启用插件
4. 开始使用新的编辑器体验

## 主要特性

- **丰富的编辑器插件**：包含多种实用插件如advlist、autolink、link、image等
- **图片上传功能**：支持直接上传图片到WordPress媒体库
- **安全性保障**：
  - 文件上传验证
  - 用户权限检查
  - 文件类型限制
  - 文件大小限制（最大2MB）
- **自定义工具栏**：提供常用的编辑功能

## 插件结构

```
tinyMCE/
├── tinymce/              # TinyMCE核心文件
├── tinymce-for-wordpress.php    # 主插件文件
├── tinymce-init.js      # TinyMCE初始化配置
└── LICENSE              # 许可证文件
```

## 许可证

本项目采用GPL2许可证。详情请查看[LICENSE](LICENSE)文件。

## 作者

- **作者**：Horace Lee
- **网站**：https://www.kuetr.cn/

## 版本

当前版本：1.0.0