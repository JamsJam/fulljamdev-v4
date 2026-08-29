import { Controller } from '@hotwired/stimulus';
import suneditor from 'suneditor';
import plugins from 'suneditor/src/plugins';
import lang from 'suneditor/src/lang';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        profile: { type: String, default: 'full' },
    };

    connect() {
        this.initEditor();
    }

    disconnect() {
        this.editor?.destroy();
        this.editor = null;
    }

    initEditor() {
        if (this.editor) {
            return;
        }

        const isBasic = this.profileValue === 'basic';

        this.editor = suneditor.create(this.element, {
            'mode': 'classic',
            'plugins': plugins,
            'rtl': false,
            'height': '120px',
            'katex': 'window.katex',
            'charCounter': true,
            'charCounterType': 'char',
            'charCounterLabel': 'characteres',
            'maxCharCount': '1000',
            'className': 'wysiwygEditor',
            'font': [
                'Montserra'
            ],
            'fontSize': [
                8, 9, 10, 11, 12, 14, 16, 18, 20, 22, 24, 26, 28, 36, 48, 72
            ],
            'fontSizeUnit': 'px',
            'formats': isBasic ? ['p'] : [
                'p', 
                'h1', 
                'h2', 
                'h3', 
                'h4', 
                'h5', 
                'h6',
                'div',
                'blockquote', 
                'pre'
            ],
            'videoFileInput': false,
            'tabDisable': false,
            'paragraphStyles': [
                'spaced',
                {
                    'name': 'Box',
                    'class': '__se__customClass'
                }
            ],
            'textStyles': [
                'translucent',
                {
                    'name': 'Emphasis',
                    'style': '-webkit-text-emphasis: filled;',
                    'tag': 'span'
                }
            ],
            'buttonList': isBasic ? [
                ['undo', 'redo', 'bold', 'italic', 'list', 'removeFormat']
            ] : [
                [
                    'undo',
                    'redo',
                    'font',
                    'fontSize',
                    'formatBlock',
                    'paragraphStyle',
                    'blockquote',
                    'bold',
                    'underline',
                    'italic',
                    'strike',
                    // "subscript",
                    // "superscript",
                    // "fontColor",
                    'hiliteColor',
                    'textStyle',
                    'removeFormat',
                    'outdent',
                    'indent',
                    'align',
                    'horizontalRule',
                    'list',
                    'lineHeight',
                    'table',
                    'link',
                    'image',
                    'video',
                    // "audio",
                    // "math",
                    // "imageGallery",
                    'fullScreen',
                    'showBlocks',
                    // "codeView",
                    'preview',
                    // "print",
                    // "save",
                    // "template"
                ]
            ],
            'lang': lang.fr,
            // "lang(In nodejs)": "fr"
        });
        this.editor.onChange = () => this.editor.save();
    }
}
