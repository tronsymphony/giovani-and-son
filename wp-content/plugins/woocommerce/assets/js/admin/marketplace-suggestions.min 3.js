!function(h,Q,w){h(function(){var l,p,t,e,g;function D(d,u,l,p,g){var t=document.createElement("a");return t.classList.add("suggestion-dismiss"),t.setAttribute("title",Q.i18n_marketplace_suggestions_dismiss_tooltip),t.setAttribute("href","#"),t.onclick=function(t){var e,s,a,n,o,i,r,c;t.preventDefault(),e=d,s=u,a=l,n=p,h("[data-suggestion-slug="+(o=g)+"]").fadeOut(function(){h(this).remove(),k()}),jQuery.post(w,{action:"woocommerce_add_dismissed_marketplace_suggestion",_wpnonce:Q.dismiss_suggestion_nonce,slug:o}),_.contains(["products-list-inline"],e)&&(i="woocommerce_snooze_suggestions__"+e,Cookies.set(i,"true",{expires:2}),r="woocommerce_dismissed_suggestions__"+e,c=parseInt(Cookies.get(r),10)||0,Cookies.set(r,c+1,{expires:31})),window.wcTracks.recordEvent("marketplace_suggestion_dismissed",{suggestion_slug:o,context:e,product:s||"",promoted:a||"",target:n||""})},t}function O(t,e,s,a,n,o,i){var r=document.createElement("a"),c=function(e,t){var s=Q.in_app_purchase_params;s.utm_source="unknown",s.utm_campaign="marketplacesuggestions",s.utm_medium="product";var a=_.findKey({productstable:["products-list-inline"],productsempty:["products-list-empty-header","products-list-empty-footer","products-list-empty-body"],ordersempty:["orders-list-empty-header","orders-list-empty-footer","orders-list-empty-body"],editproduct:["product-edit-meta-tab-header","product-edit-meta-tab-footer","product-edit-meta-tab-body"]},function(t){return _.contains(t,e)});return a&&(s.utm_source=a),t+"?"+jQuery.param(s)}(t,n);r.setAttribute("href",c);var d;return _.includes(["product-edit-meta-tab-header","product-edit-meta-tab-footer","product-edit-meta-tab-body"],t)&&r.setAttribute("target","blank"),r.textContent=o,r.onclick=function(){window.wcTracks.recordEvent("marketplace_suggestion_clicked",{suggestion_slug:a,context:t,product:e||"",promoted:s||"",target:n||""})},i?r.classList.add("button"):(r.classList.add("linkout"),(d=document.createElement("span")).classList.add("dashicons","dashicons-external"),r.appendChild(d)),r}function m(t,e,s,a,n,o,i,r,c,d,u){var l=document.createElement("div");l.classList.add("marketplace-suggestion-container"),l.dataset.suggestionSlug=a;var p,g,m,_,k,f,h,w,b,v,y,x,C,E,L,T,A,j=function(t){if(!t)return null;var e=document.createElement("img");return e.src=t,e.classList.add("marketplace-suggestion-icon"),e}(n);return j&&l.appendChild(j),l.appendChild((p=a,g=o,m=i,(h=document.createElement("div")).classList.add("marketplace-suggestion-container-content"),g&&((_=document.createElement("h4")).textContent=g,h.appendChild(_)),m&&((k=document.createElement("p")).textContent=m,h.appendChild(k)),-1!==["product-edit-empty-footer-browse-all","product-edit-meta-tab-footer-browse-all"].indexOf(p)&&(h.classList.add("has-manage-link"),(f=document.createElement("a")).classList.add("marketplace-suggestion-manage-link","linkout"),f.setAttribute("href",Q.manage_suggestions_url),f.textContent=Q.i18n_marketplace_suggestions_manage_suggestions,h.appendChild(f)),h)),l.appendChild((w=t,b=e,v=s,y=a,x=r,C=c,E=d,L=u,A=document.createElement("div"),C=C||Q.i18n_marketplace_suggestions_default_cta,A.classList.add("marketplace-suggestion-container-cta"),x&&C&&(T=O(w,b,v,y,x,C,E),A.appendChild(T)),L&&A.appendChild(D(w,b,v,x,y)),A)),l}function k(){h('.marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-body"]').children().length<=0&&h('.marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-body"], .marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-header"], .marketplace-suggestions-container[data-marketplace-suggestions-context="product-edit-meta-tab-footer"]').fadeOut({complete:function(){h(".marketplace-suggestions-metabox-nosuggestions-placeholder").fadeIn()}})}function f(t){return _.includes(["product-edit-meta-tab-header","product-edit-meta-tab-body","product-edit-meta-tab-footer"],t)}void 0!==Q&&(window.wcTracks=window.wcTracks||{},window.wcTracks.recordEvent=window.wcTracks.recordEvent||function(){},l=!1,Q.suggestions_data&&(p=Q.suggestions_data,g=[],h(".marketplace-suggestions-container").each(function(){var t,e,s,a=this.dataset.marketplaceSuggestionsContext,n=(t=p,e=a,s=_.filter(t,function(t){return _.isArray(t.context)?_.contains(t.context,e):e===t.context}),s=_.filter(s,function(t){return!_.contains(Q.dismissed_suggestions,t.slug)}),s=_.filter(s,function(t){return!_.contains(Q.active_plugins,t.product)}),_.filter(s,function(t){return!t["show-if-active"]||0<_.intersection(Q.active_plugins,t["show-if-active"]).length})),o=_.sample(n,5);for(var i in o){var r=o[i]["link-text"],c=!0;o[i]["link-text"]&&(r=o[i]["link-text"],c=!1);var d=!0;!1===o[i]["allow-dismiss"]&&(d=!1);var u=m(a,o[i].product,o[i].promoted,o[i].slug,o[i].icon,o[i].title,o[i].copy,o[i].url,r,c,d);h(this).append(u),h(this).addClass("showing-suggestion"),g.push(a),f(a)||window.wcTracks.recordEvent("marketplace_suggestion_displayed",{suggestion_slug:o[i].slug,context:a,product:o[i].product||"",promoted:o[i].promoted||"",target:o[i].url||""})}h("ul.product_data_tabs li.marketplace-suggestions_options a").click(function(t){if(t.preventDefault(),"#marketplace_suggestions"!==l&&f(a))for(var e in o)window.wcTracks.recordEvent("marketplace_suggestion_displayed",{suggestion_slug:o[e].slug,context:a,product:o[e].product||"",promoted:o[e].promoted||"",target:o[e].url||""})})}),t=g,(e=0<_.intersection(t,["products-list-empty-body","orders-list-empty-body"]).length)&&(h("#screen-meta-links").hide(),h("#wpfooter").hide()),e||(h('.marketplace-suggestions-container[data-marketplace-suggestions-context="products-list-empty-header"]').hide(),h('.marketplace-suggestions-container[data-marketplace-suggestions-context="products-list-empty-footer"]').hide(),h('.marketplace-suggestions-container[data-marketplace-suggestions-context="orders-list-empty-header"]').hide(),h('.marketplace-suggestions-container[data-marketplace-suggestions-context="orders-list-empty-footer"]').hide()),k(),h("ul.product_data_tabs").on("click","li a",function(t){t.preventDefault(),l=h(this).attr("href")})),h("a.marketplace-suggestion-manage-link").on("click",function(){window.wcTracks.recordEvent("marketplace_suggestions_manage_clicked")}))})}(jQuery,marketplace_suggestions,ajaxurl);