function wcSetClipboard(e,r){void 0===r&&(r=jQuery(document));var t=jQuery('<textarea style="opacity:0">');jQuery("body").append(t),t.val(e).select(),r.trigger("beforecopy");try{document.execCommand("copy"),r.trigger("aftercopy")}catch(o){r.trigger("aftercopyfailure")}t.remove()}function wcClearClipboard(){wcSetClipboard("")}