// WYSIWYG editor integration
import tinymce from 'tinymce/tinymce';

// Import TinyMCE themes and plugins
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE editor for post content
    const contentTextarea = document.querySelector('#content');
    if (contentTextarea) {
        tinymce.init({
            selector: '#content',
            height: 500,
            skin: false,
            content_css: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | link image | code preview | help',
            menubar: false,
            branding: false,
            promotion: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            content_style: `
                body {
                    font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    font-size: 16px;
                    line-height: 1.6;
                    color: #334155;
                    max-width: none;
                    margin: 1rem;
                }
                h1, h2, h3, h4, h5, h6 {
                    font-weight: 600;
                    color: #0f172a;
                    margin-top: 1.5rem;
                    margin-bottom: 0.5rem;
                }
                p {
                    margin-bottom: 1rem;
                }
                blockquote {
                    border-left: 4px solid #0ea5e9;
                    padding-left: 1.5rem;
                    margin: 1.5rem 0;
                    font-style: italic;
                    color: #475569;
                }
                img {
                    max-width: 100%;
                    height: auto;
                    border-radius: 0.5rem;
                }
            `,
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });
    }
});