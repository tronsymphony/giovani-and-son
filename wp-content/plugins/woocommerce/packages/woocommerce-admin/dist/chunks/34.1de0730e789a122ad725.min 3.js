(window.__wcAdmin_webpackJsonp=window.__wcAdmin_webpackJsonp||[]).push([[34],{739:function(e,t,r){"use strict";r.d(t,"a",(function(){return g})),r.d(t,"e",(function(){return y})),r.d(t,"b",(function(){return j})),r.d(t,"f",(function(){return h})),r.d(t,"d",(function(){return w})),r.d(t,"c",(function(){return R}));var a=r(5),n=r.n(a),o=r(53),i=r.n(o),s=r(2),c=r(17),l=r.n(c),u=r(115),d=r(29),b=r(36),f=r(739);function p(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,a)}return r}function m(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?p(Object(r),!0).forEach((function(t){n()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):p(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function g(e){var t=e.endpoint,r=e.query,a=e.limitBy,o=e.filters,c=void 0===o?[]:o,b=e.advancedFilters,f=void 0===b?{}:b;return r.search?(a||[t]).reduce((function(e,t){return e[t]=r[t],e}),{}):c.map((function(e){return function(e,t,r){var a=r[e.param];if(!a)return{};if("advanced"===a){var o=Object(d.getActiveFiltersFromQuery)(r,t.filters);return 0===o.length?{}:o.map((function(e){return function(e,t){var r=e.filters[t.key];if("Date"!==Object(s.get)(r,["input","component"]))return t;var a=t.rule,n=t.value,o={after:"start",before:"end"};if(Array.isArray(n)){var c=i()(n,2),d=c[0],b=c[1];return Object.assign({},t,{value:[Object(u.a)(l()(d),o.after),Object(u.a)(l()(b),o.before)]})}return Object.assign({},t,{value:Object(u.a)(l()(n),o[a])})}(t,e)})).reduce((function(e,t){var r=t.key,a=t.rule,n=t.value;return e[Object(d.getUrlKey)(r,a)]=n,e}),{match:r.match||"all"})}var c=Object(s.find)(Object(d.flattenFilters)(e.filters),{value:a});if(!c)return{};if(c.settings&&c.settings.param){var b=c.settings.param;return r[b]?n()({},b,r[b]):{}}return n()({},e.param,a)}(e,f,r)})).reduce((function(e,t){return Object.assign(e,t)}),{})}var v=["stock","customers"];function O(e){var t=e.endpoint,r=e.dataType,a=e.query,n=e.fields,o=Object(u.f)(a,e.defaultDateRange),i=Object(u.i)(a),c=g(e),l=o[r].before;return Object(s.includes)(v,t)?m(m({},c),{},{fields:n}):m({order:"asc",interval:i,per_page:b.b,after:Object(u.a)(o[r].after,"start"),before:Object(u.a)(l,"end"),segmentby:a.segmentby,fields:n},c)}function y(e){var t=e.endpoint,r=(0,e.select)("wc-api"),a=r.getReportStats,n=r.getReportStatsError,o=r.isReportStatsRequesting,i={isRequesting:!1,isError:!1,totals:{primary:null,secondary:null}},s=O(m(m({},e),{},{dataType:"primary"})),c=a(t,s);if(o(t,s))return m(m({},i),{},{isRequesting:!0});if(n(t,s))return m(m({},i),{},{isError:!0});var l=c&&c.data&&c.data.totals||null,u=O(m(m({},e),{},{dataType:"secondary"})),d=a(t,u);if(o(t,u))return m(m({},i),{},{isRequesting:!0});if(n(t,u))return m(m({},i),{},{isError:!0});var b=d&&d.data&&d.data.totals||null;return m(m({},i),{},{totals:{primary:l,secondary:b}})}function j(e){var t=e.endpoint,r=(0,e.select)("wc-api"),a=r.getReportStats,n=r.getReportStatsError,o=r.isReportStatsRequesting,i={isEmpty:!1,isError:!1,isRequesting:!1,data:{totals:{},intervals:[]}},c=O(e),l=a(t,c);if(o(t,c))return m(m({},i),{},{isRequesting:!0});if(n(t,c))return m(m({},i),{},{isError:!0});if(function(e,t){return!e||(!e.data||(!(e.data.totals&&!Object(s.isNull)(e.data.totals))||!(Object(s.includes)(v,t)||e.data.intervals&&0!==e.data.intervals.length)))}(l,t))return m(m({},i),{},{isEmpty:!0});var u=l&&l.data&&l.data.totals||null,d=l&&l.data&&l.data.intervals||[];if(l.totalResults>b.b){for(var f=!0,p=!1,g=[],y=Math.ceil(l.totalResults/b.b),j=1,h=2;h<=y;h++){var w=m(m({},c),{},{page:h}),R=a(t,w);if(!o(t,w)){if(n(t,w)){p=!0,f=!1;break}if(g.push(R),++j===y){f=!1;break}}}if(f)return m(m({},i),{},{isRequesting:!0});if(p)return m(m({},i),{},{isError:!0});Object(s.forEach)(g,(function(e){d=d.concat(e.data.intervals)}))}return m(m({},i),{},{data:{totals:u,intervals:d}})}function h(e,t){switch(e){case"currency":return t;case"percent":return".0%";case"number":return",";case"average":return",.2r";default:return","}}function w(e){var t=e.query,r=e.tableQuery,a=void 0===r?{}:r,n=g(e),o=Object(u.f)(t,e.defaultDateRange),i=Object(s.includes)(v,e.endpoint);return m(m({orderby:t.orderby||"date",order:t.order||"desc",after:i?void 0:Object(u.a)(o.primary.after,"start"),before:i?void 0:Object(u.a)(o.primary.before,"end"),page:t.paged||1,per_page:t.per_page||b.d.pageSize},n),a)}function R(e){var t=e.endpoint,r=(0,e.select)("wc-api"),a=r.getReportItems,n=r.getReportItemsError,o=r.isReportItemsRequesting,i=f.d(e),s={query:i,isRequesting:!1,isError:!1,items:{data:[],totalResults:0}},c=a(t,i);return o(t,i)?m(m({},s),{},{isRequesting:!0}):n(t,i)?m(m({},s),{},{isError:!0}):m(m({},s),{},{items:c})}},746:function(e,t,r){"use strict";var a=r(756),n=["a","b","em","i","strong","p"],o=["target","href","rel","name","download"];t.a=function(e){return{__html:Object(a.sanitize)(e,{ALLOWED_TAGS:n,ALLOWED_ATTR:o})}}},760:function(e,t,r){"use strict";r.d(t,"a",(function(){return u}));var a=r(8),n=r(27),o=r(19),i=r(0),s=r(2),c=r(279),l=r(742);function u(e){var t=e.help,r=e.label,d=e.multiple,b=void 0!==d&&d,f=e.onChange,p=e.options,m=void 0===p?[]:p,g=e.className,v=e.hideLabelFromVision,O=Object(o.a)(e,["help","label","multiple","onChange","options","className","hideLabelFromVision"]),y=Object(c.a)(u),j="inspector-select-control-".concat(y);return!Object(s.isEmpty)(m)&&Object(i.createElement)(l.a,{label:r,hideLabelFromVision:v,id:j,help:t,className:g},Object(i.createElement)("select",Object(a.a)({id:j,className:"components-select-control__input",onChange:function(e){if(b){var t=Object(n.a)(e.target.options).filter((function(e){return e.selected})).map((function(e){return e.value}));f(t)}else f(e.target.value)},"aria-describedby":t?"".concat(j,"__help"):void 0,multiple:b},O),m.map((function(e,t){return Object(i.createElement)("option",{key:"".concat(e.label,"-").concat(e.value,"-").concat(t),value:e.value,disabled:e.disabled},e.label)}))))}},913:function(e,t,r){},914:function(e,t,r){},938:function(e,t,r){"use strict";r.r(t);var a=r(53),n=r.n(a),o=r(49),i=r.n(o),s=r(0),c=r(3),l=r(185),u=r(1),d=r.n(u),b=r(760),f=r(55),p=r(46),m=r(28),g=r(13),v=r.n(g),O=r(12),y=r.n(O),j=r(14),h=r.n(j),w=r(15),R=r.n(w),E=r(6),_=r.n(E),q=r(29),k=r(296),T=r(277),L=r(746),S=r(104),P=r(739);r(913);function I(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,a=_()(e);if(t){var n=_()(this).constructor;r=Reflect.construct(a,arguments,n)}else r=a.apply(this,arguments);return R()(this,r)}}var F=function(e){h()(r,e);var t=I(r);function r(){return v()(this,r),t.apply(this,arguments)}return y()(r,[{key:"getFormattedHeaders",value:function(){return this.props.headers.map((function(e,t){return{isLeftAligned:0===t,hiddenByDefault:!1,isSortable:!1,key:e.label,label:e.label}}))}},{key:"getFormattedRows",value:function(){return this.props.rows.map((function(e){return e.map((function(e){return{display:Object(s.createElement)("div",{dangerouslySetInnerHTML:Object(L.a)(e.display)}),value:e.value}}))}))}},{key:"render",value:function(){var e=this.props,t=e.isRequesting,r=e.isError,a=e.totalRows,n=e.title,o="woocommerce-leaderboard";if(r)return Object(s.createElement)(T.a,{className:o,isError:!0});var i=this.getFormattedRows();return t||0!==i.length?Object(s.createElement)(f.TableCard,{className:o,headers:this.getFormattedHeaders(),isLoading:t,rows:i,rowsPerPage:a,showMenu:!1,title:n,totalRows:a}):Object(s.createElement)(f.Card,{title:n,className:o},Object(s.createElement)(f.EmptyTable,null,Object(c.__)("No data recorded for the selected time period.",'woocommerce')))}}]),r}(s.Component);F.propTypes={headers:d.a.arrayOf(d.a.shape({label:d.a.string})),id:d.a.string.isRequired,query:d.a.object,rows:d.a.arrayOf(d.a.arrayOf(d.a.shape({display:d.a.node,value:d.a.oneOfType([d.a.string,d.a.number,d.a.bool])}))).isRequired,title:d.a.string.isRequired,totalRows:d.a.number.isRequired},F.defaultProps={rows:[],isError:!1,isRequesting:!1};var A=Object(l.a)(Object(S.a)((function(e,t){var r=t.id,a=t.query,n=t.totalRows,o=t.filters,i=e(p.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings").woocommerce_default_date_range,s=Object(P.a)({filters:o,query:a}),c={id:r,per_page:n,persisted_query:Object(q.getPersistedQuery)(a),query:a,select:e,defaultDateRange:i,filterQuery:s};return Object(k.a)(c)})))(F),N=r(63),C=(r(914),function(e){var t=e.allLeaderboards,r=e.controls,a=e.isFirst,o=e.isLast,l=e.hiddenBlocks,u=e.onMove,d=e.onRemove,m=e.onTitleBlur,g=e.onTitleChange,v=e.onToggleHiddenBlock,O=e.query,y=e.title,j=e.titleInput,h=e.filters,w=Object(p.useUserPreferences)(),R=w.updateUserPreferences,E=i()(w,["updateUserPreferences"]),_=Object(s.useState)(parseInt(E.dashboard_leaderboard_rows||5,10)),q=n()(_,2),k=q[0],T=q[1],L=function(e){T(parseInt(e,10));var t={dashboard_leaderboard_rows:parseInt(e,10)};R(t)};return Object(s.createElement)(s.Fragment,null,Object(s.createElement)("div",{className:"woocommerce-dashboard__dashboard-leaderboards"},Object(s.createElement)(f.SectionHeader,{title:y||Object(c.__)("Leaderboards",'woocommerce'),menu:Object(s.createElement)(f.EllipsisMenu,{label:Object(c.__)("Choose which leaderboards to display and other settings",'woocommerce'),renderContent:function(e){var n=e.onToggle;return Object(s.createElement)(s.Fragment,null,Object(s.createElement)(f.MenuTitle,null,Object(c.__)("Leaderboards",'woocommerce')),function(e){var t=e.allLeaderboards,r=e.hiddenBlocks,a=e.onToggleHiddenBlock;return t.map((function(e){var t=!r.includes(e.id);return Object(s.createElement)(f.MenuItem,{checked:t,isCheckbox:!0,isClickable:!0,key:e.id,onInvoke:function(){a(e.id)(),Object(N.b)("dash_leaderboards_toggle",{status:t?"off":"on",key:e.id})}},e.label)}))}({allLeaderboards:t,hiddenBlocks:l,onToggleHiddenBlock:v}),Object(s.createElement)(b.a,{className:"woocommerce-dashboard__dashboard-leaderboards__select",label:Object(c.__)("Rows Per Table",'woocommerce'),value:k,options:Array.from({length:20},(function(e,t){return{v:t+1,label:t+1}})),onChange:L}),window.wcAdminFeatures["analytics-dashboard/customizable"]&&Object(s.createElement)(r,{onToggle:n,onMove:u,onRemove:d,isFirst:a,isLast:o,onTitleBlur:m,onTitleChange:g,titleInput:j}))}})}),Object(s.createElement)("div",{className:"woocommerce-dashboard__columns"},function(e){var t=e.allLeaderboards,r=e.hiddenBlocks,a=e.query,n=e.rowsPerTable,o=e.filters;return t.map((function(e){if(!r.includes(e.id))return Object(s.createElement)(A,{headers:e.headers,id:e.id,key:e.id,query:a,title:e.label,totalRows:n,filters:o})}))}({allLeaderboards:t,hiddenBlocks:l,query:O,rowsPerTable:k,filters:h}))))});C.propTypes={query:d.a.object.isRequired};t.default=Object(l.a)(Object(S.a)((function(e){var t=e("wc-api"),r=t.getItems,a=t.getItemsError,n=t.isGetItemsRequesting;return{allLeaderboards:Object(m.g)("dataEndpoints",{leaderboards:[]}).leaderboards,getItems:r,getItemsError:a,isGetItemsRequesting:n}})))(C)}}]);