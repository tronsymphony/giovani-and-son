(window.__wcAdmin_webpackJsonp=window.__wcAdmin_webpackJsonp||[]).push([[15],{729:function(e,t,r){"use strict";r.r(t),r.d(t,"default",(function(){return V}));var a=r(13),n=r.n(a),o=r(12),c=r.n(o),i=r(14),s=r.n(i),u=r(15),l=r.n(u),d=r(6),m=r.n(d),p=r(0),f=r(1),b=r.n(f),y=r(772),h=r(7),_=r.n(h),v=r(3),g=r(24),O=r(2),j=r(17),w=r.n(j),R=r(115),D=r(55),q=r(29),S=r(219),k=r(28),C=r(46),P=r(749),E=r(218);function x(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,a=m()(e);if(t){var n=m()(this).constructor;r=Reflect.construct(a,arguments,n)}else r=a.apply(this,arguments);return l()(this,r)}}var T=function(e){s()(r,e);var t=x(r);function r(){var e;return n()(this,r),(e=t.call(this)).getHeadersContent=e.getHeadersContent.bind(_()(e)),e.getRowsContent=e.getRowsContent.bind(_()(e)),e.getSummary=e.getSummary.bind(_()(e)),e}return c()(r,[{key:"getHeadersContent",value:function(){return[{label:Object(v.__)("Date",'woocommerce'),key:"date",defaultSort:!0,required:!0,isLeftAligned:!0,isSortable:!0},{label:Object(v.__)("Product Title",'woocommerce'),key:"product",isSortable:!0,required:!0},{label:Object(v.__)("File Name",'woocommerce'),key:"file_name"},{label:Object(v.__)("Order #",'woocommerce'),screenReaderLabel:Object(v.__)("Order Number",'woocommerce'),key:"order_number"},{label:Object(v.__)("Username",'woocommerce'),key:"user_id"},{label:Object(v.__)("IP",'woocommerce'),key:"ip_address"}]}},{key:"getRowsContent",value:function(e){var t=this.props.query,r=Object(q.getPersistedQuery)(t),a=Object(k.g)("dateFormat",R.c);return Object(O.map)(e,(function(e){var t,n,o=e._embedded,c=e.date,i=e.file_name,s=e.file_path,u=e.ip_address,l=e.order_id,d=e.order_number,m=e.product_id,f=e.username,b=o.product[0],y=b.code,h=b.name;if("woocommerce_rest_product_invalid_id"===y)t=Object(v.__)("(Deleted)",'woocommerce'),n=Object(v.__)("(Deleted)",'woocommerce');else{var _=Object(q.getNewPath)(r,"/analytics/products",{filter:"single_product",products:m});t=Object(p.createElement)(D.Link,{href:_,type:"wc-admin"},h),n=h}return[{display:Object(p.createElement)(D.Date,{date:c,visibleFormat:a}),value:c},{display:t,value:n},{display:Object(p.createElement)(D.Link,{href:s,type:"external"},i),value:i},{display:Object(p.createElement)(D.Link,{href:Object(k.f)("post.php?post=".concat(l,"&action=edit")),type:"wp-admin"},d),value:d},{display:f,value:f},{display:u,value:u}]}))}},{key:"getSummary",value:function(e){var t=e.download_count,r=void 0===t?0:t,a=this.props,n=a.query,o=a.defaultDateRange,c=Object(R.f)(n,o),i=w()(c.primary.after),s=w()(c.primary.before).diff(i,"days")+1,u=this.context.getCurrencyConfig();return[{label:Object(v._n)("day","days",s,'woocommerce'),value:Object(S.formatValue)(u,"number",s)},{label:Object(v._n)("download","downloads",r,'woocommerce'),value:Object(S.formatValue)(u,"number",r)}]}},{key:"render",value:function(){var e=this.props,t=e.query,r=e.filters,a=e.advancedFilters;return Object(p.createElement)(P.a,{endpoint:"downloads",getHeadersContent:this.getHeadersContent,getRowsContent:this.getRowsContent,getSummary:this.getSummary,summaryFields:["download_count"],query:t,tableQuery:{_embed:!0},title:Object(v.__)("Downloads",'woocommerce'),columnPrefsKey:"downloads_report_columns",filters:r,advancedFilters:a})}}]),r}(p.Component);T.contextType=E.a;var F=Object(g.withSelect)((function(e){return{defaultDateRange:e(C.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings").woocommerce_default_date_range}}))(T),I=r(744),L=r(743),A=r(745),H=r(747);function N(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,a=m()(e);if(t){var n=m()(this).constructor;r=Reflect.construct(a,arguments,n)}else r=a.apply(this,arguments);return l()(this,r)}}var V=function(e){s()(r,e);var t=N(r);function r(){return n()(this,r),t.apply(this,arguments)}return c()(r,[{key:"render",value:function(){var e=this.props,t=e.query,r=e.path;return Object(p.createElement)(p.Fragment,null,Object(p.createElement)(H.a,{query:t,path:r,filters:y.c,advancedFilters:y.a,report:"downloads"}),Object(p.createElement)(A.a,{charts:y.b,endpoint:"downloads",query:t,selectedChart:Object(I.a)(t.chart,y.b),filters:y.c,advancedFilters:y.a}),Object(p.createElement)(L.a,{charts:y.b,endpoint:"downloads",path:r,query:t,selectedChart:Object(I.a)(t.chart,y.b),filters:y.c,advancedFilters:y.a}),Object(p.createElement)(F,{query:t,filters:y.c,advancedFilters:y.a}))}}]),r}(p.Component);V.propTypes={query:b.a.object.isRequired}},740:function(e,t,r){"use strict";r.d(t,"e",(function(){return l})),r.d(t,"a",(function(){return d})),r.d(t,"b",(function(){return m})),r.d(t,"c",(function(){return p})),r.d(t,"d",(function(){return f})),r.d(t,"f",(function(){return b})),r.d(t,"g",(function(){return y}));var a=r(34),n=r(31),o=r.n(n),c=r(2),i=r(29),s=r(741),u=r(36);function l(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:c.identity;return function(){var r=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",n=arguments.length>1?arguments[1]:void 0,c="function"==typeof e?e(n):e,s=Object(i.getIdsFromQuery)(r);if(s.length<1)return Promise.resolve([]);var u={include:s.join(","),per_page:s.length};return o()({path:Object(a.addQueryArgs)(c,u)}).then((function(e){return e.map(t)}))}}var d=l(u.c+"/products/categories",(function(e){return{key:e.id,label:e.name}})),m=l(u.c+"/coupons",(function(e){return{key:e.id,label:e.code}})),p=l(u.c+"/customers",(function(e){return{key:e.id,label:e.name}})),f=l(u.c+"/products",(function(e){return{key:e.id,label:e.name}})),b=l(u.c+"/taxes",(function(e){return{key:e.id,label:Object(s.a)(e)}})),y=l((function(e){return u.c+"/products/".concat(e.products,"/variations")}),(function(e){return{key:e.id,label:e.attributes.reduce((function(e,t,r,a){return e+"".concat(t.option).concat(a.length===r+1?"":", ")}),"")}}))},741:function(e,t,r){"use strict";r.d(t,"a",(function(){return n}));var a=r(3);function n(e){return[e.country,e.state,e.name||Object(a.__)("TAX",'woocommerce'),e.priority].map((function(e){return e.toString().toUpperCase().trim()})).filter(Boolean).join("-")}},743:function(e,t,r){"use strict";var a=r(5),n=r.n(a),o=r(13),c=r.n(o),i=r(12),s=r.n(i),u=r(14),l=r.n(u),d=r(15),m=r.n(d),p=r(6),f=r.n(p),b=r(0),y=r(3),h=r(185),_=r(117),v=r(2),g=r(1),O=r.n(g),j=r(115),w=r(55),R=r(46),D=r(218),q=r(739),S=r(277),k=r(104),C=r(29);function P(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,a)}return r}function E(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?P(Object(r),!0).forEach((function(t){n()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):P(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function x(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,a=f()(e);if(t){var n=f()(this).constructor;r=Reflect.construct(a,arguments,n)}else r=a.apply(this,arguments);return m()(this,r)}}var T=function(e){l()(r,e);var t=x(r);function r(){return c()(this,r),t.apply(this,arguments)}return s()(r,[{key:"shouldComponentUpdate",value:function(e){return e.isRequesting!==this.props.isRequesting||e.primaryData.isRequesting!==this.props.primaryData.isRequesting||e.secondaryData.isRequesting!==this.props.secondaryData.isRequesting||!Object(v.isEqual)(e.query,this.props.query)}},{key:"getItemChartData",value:function(){var e=this.props,t=e.primaryData,r=e.selectedChart;return t.data.intervals.map((function(e){var t={};return e.subtotals.segments.forEach((function(e){if(e.segment_label){var a=t[e.segment_label]?e.segment_label+" (#"+e.segment_id+")":e.segment_label;t[e.segment_id]={label:a,value:e.subtotals[r.key]||0}}})),E({date:Object(_.a)("Y-m-d\\TH:i:s",e.date_start)},t)}))}},{key:"getTimeChartData",value:function(){var e=this.props,t=e.query,r=e.primaryData,a=e.secondaryData,n=e.selectedChart,o=e.defaultDateRange,c=Object(j.i)(t),i=Object(j.f)(t,o),s=i.primary,u=i.secondary;return r.data.intervals.map((function(e,r){var o=Object(j.j)(e.date_start,s.after,u.after,t.compare,c),i=a.data.intervals[r];return{date:Object(_.a)("Y-m-d\\TH:i:s",e.date_start),primary:{label:"".concat(s.label," (").concat(s.range,")"),labelDate:e.date_start,value:e.subtotals[n.key]||0},secondary:{label:"".concat(u.label," (").concat(u.range,")"),labelDate:o.format("YYYY-MM-DD HH:mm:ss"),value:i&&i.subtotals[n.key]||0}}}))}},{key:"getTimeChartTotals",value:function(){var e=this.props,t=e.primaryData,r=e.secondaryData,a=e.selectedChart;return{primary:Object(v.get)(t,["data","totals",a.key],null),secondary:Object(v.get)(r,["data","totals",a.key],null)}}},{key:"renderChart",value:function(e,t,r,a){var n=this.props,o=n.emptySearchResults,c=n.filterParam,i=n.interactiveLegend,s=n.itemsLabel,u=n.legendPosition,l=n.path,d=n.query,m=n.selectedChart,p=n.showHeaderControls,f=n.primaryData,h=Object(j.i)(d),_=Object(j.d)(d),v=Object(j.g)(h,f.data.intervals.length),g=o?Object(y.__)("No data for the current search",'woocommerce'):Object(y.__)("No data for the selected date range",'woocommerce'),O=this.context,R=O.formatAmount,D=O.getCurrencyConfig;return Object(b.createElement)(w.Chart,{allowedIntervals:_,data:r,dateParser:"%Y-%m-%dT%H:%M:%S",emptyMessage:g,filterParam:c,interactiveLegend:i,interval:h,isRequesting:t,itemsLabel:s,legendPosition:u,legendTotals:a,mode:e,path:l,query:d,screenReaderFormat:v.screenReaderFormat,showHeaderControls:p,title:m.label,tooltipLabelFormat:v.tooltipLabelFormat,tooltipTitle:"time-comparison"===e&&m.label||null,tooltipValueFormat:Object(q.f)(m.type,R),chartType:Object(j.e)(d),valueType:m.type,xFormat:v.xFormat,x2Format:v.x2Format,currency:D()})}},{key:"renderItemComparison",value:function(){var e=this.props,t=e.isRequesting,r=e.primaryData;if(r.isError)return Object(b.createElement)(S.a,{isError:!0});var a=t||r.isRequesting,n=this.getItemChartData();return this.renderChart("item-comparison",a,n)}},{key:"renderTimeComparison",value:function(){var e=this.props,t=e.isRequesting,r=e.primaryData,a=e.secondaryData;if(!r||r.isError||a.isError)return Object(b.createElement)(S.a,{isError:!0});var n=t||r.isRequesting||a.isRequesting,o=this.getTimeChartData(),c=this.getTimeChartTotals();return this.renderChart("time-comparison",n,o,c)}},{key:"render",value:function(){return"item-comparison"===this.props.mode?this.renderItemComparison():this.renderTimeComparison()}}]),r}(b.Component);T.contextType=D.a,T.propTypes={filters:O.a.array,isRequesting:O.a.bool,itemsLabel:O.a.string,limitProperties:O.a.array,mode:O.a.string,path:O.a.string.isRequired,primaryData:O.a.object,query:O.a.object.isRequired,secondaryData:O.a.object,selectedChart:O.a.shape({key:O.a.string.isRequired,label:O.a.string.isRequired,order:O.a.oneOf(["asc","desc"]),orderby:O.a.string,type:O.a.oneOf(["average","number","currency"]).isRequired}).isRequired},T.defaultProps={isRequesting:!1,primaryData:{data:{intervals:[]},isError:!1,isRequesting:!1},secondaryData:{data:{intervals:[]},isError:!1,isRequesting:!1}};t.a=Object(h.a)(Object(k.a)((function(e,t){var r=t.charts,a=t.endpoint,n=t.filters,o=t.isRequesting,c=t.limitProperties,i=t.query,s=t.advancedFilters,u=c||[a],l=function e(t,r){var a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};if(!t||0===t.length)return null;var n=t.slice(0),o=n.pop();if(o.showFilters(r,a)){var c=Object(C.flattenFilters)(o.filters),i=r[o.param]||o.defaultValue||"all";return Object(v.find)(c,{value:i})}return e(n,r,a)}(n,i),d=Object(v.get)(l,["settings","param"]),m=t.mode||function(e,t){if(e&&t){var r=Object(v.get)(e,["settings","param"]);if(!r||Object.keys(t).includes(r))return Object(v.get)(e,["chartMode"])}return null}(l,i)||"time-comparison",p=e(R.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings").woocommerce_default_date_range,f={mode:m,filterParam:d,defaultDateRange:p};if(o)return f;var b=u.some((function(e){return i[e]&&i[e].length}));if(i.search&&!b)return E(E({},f),{},{emptySearchResults:!0});var y=r&&r.map((function(e){return e.key})),h=Object(q.b)({endpoint:a,dataType:"primary",query:i,select:e,limitBy:u,filters:n,advancedFilters:s,defaultDateRange:p,fields:y});if("item-comparison"===m)return E(E({},f),{},{primaryData:h});var _=Object(q.b)({endpoint:a,dataType:"secondary",query:i,select:e,limitBy:u,filters:n,advancedFilters:s,defaultDateRange:p,fields:y});return E(E({},f),{},{primaryData:h,secondaryData:_})})))(T)},744:function(e,t,r){"use strict";r.d(t,"a",(function(){return n}));var a=r(2);function n(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[],r=Object(a.find)(t,{key:e});return r||t[0]}},745:function(e,t,r){"use strict";var a=r(13),n=r.n(a),o=r(12),c=r.n(o),i=r(14),s=r.n(i),u=r(15),l=r.n(u),d=r(6),m=r.n(d),p=r(0),f=r(3),b=r(185),y=r(1),h=r.n(y),_=r(115),v=r(29),g=r(55),O=r(219),j=r(46),w=r(739),R=r(277),D=r(104),q=r(63),S=r(218);function k(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,a=m()(e);if(t){var n=m()(this).constructor;r=Reflect.construct(a,arguments,n)}else r=a.apply(this,arguments);return l()(this,r)}}var C=function(e){s()(r,e);var t=k(r);function r(){return n()(this,r),t.apply(this,arguments)}return c()(r,[{key:"formatVal",value:function(e,t){var r=this.context,a=r.formatAmount,n=r.getCurrencyConfig;return"currency"===t?a(e):Object(O.formatValue)(n(),t,e)}},{key:"getValues",value:function(e,t){var r=this.props,a=r.emptySearchResults,n=r.summaryData.totals,o=a?0:n.primary[e],c=a?0:n.secondary[e];return{delta:Object(O.calculateDelta)(o,c),prevValue:this.formatVal(c,t),value:this.formatVal(o,t)}}},{key:"render",value:function(){var e=this,t=this.props,r=t.charts,a=t.query,n=t.selectedChart,o=t.summaryData,c=t.endpoint,i=t.report,s=t.defaultDateRange,u=o.isError,l=o.isRequesting;if(u)return Object(p.createElement)(R.a,{isError:!0});if(l)return Object(p.createElement)(g.SummaryListPlaceholder,{numberOfItems:r.length});var d=Object(_.h)(a,s).compare;return Object(p.createElement)(g.SummaryList,null,(function(t){var a=t.onToggle;return r.map((function(t){var r=t.key,o=t.order,s=t.orderby,u=t.label,l=t.type,m={chart:r};s&&(m.orderby=s),o&&(m.order=o);var b=Object(v.getNewPath)(m),y=n.key===r,h=e.getValues(r,l),_=h.delta,O=h.prevValue,j=h.value;return Object(p.createElement)(g.SummaryNumber,{key:r,delta:_,href:b,label:u,prevLabel:"previous_period"===d?Object(f.__)("Previous Period:",'woocommerce'):Object(f.__)("Previous Year:",'woocommerce'),prevValue:O,selected:y,value:j,onLinkClickCallback:function(){a&&a(),Object(q.b)("analytics_chart_tab_click",{report:i||c,key:r})}})}))}))}}]),r}(p.Component);C.propTypes={charts:h.a.array.isRequired,endpoint:h.a.string.isRequired,limitProperties:h.a.array,query:h.a.object.isRequired,selectedChart:h.a.shape({key:h.a.string.isRequired,label:h.a.string.isRequired,order:h.a.oneOf(["asc","desc"]),orderby:h.a.string,type:h.a.oneOf(["average","number","currency"]).isRequired}).isRequired,summaryData:h.a.object,report:h.a.string},C.defaultProps={summaryData:{totals:{primary:{},secondary:{}},isError:!1}},C.contextType=S.a,t.a=Object(b.a)(Object(D.a)((function(e,t){var r=t.charts,a=t.endpoint,n=t.limitProperties,o=t.query,c=t.filters,i=t.advancedFilters,s=n||[a],u=s.some((function(e){return o[e]&&o[e].length}));if(o.search&&!u)return{emptySearchResults:!0};var l=r&&r.map((function(e){return e.key})),d=e(j.SETTINGS_STORE_NAME).getSetting("wc_admin","wcAdminSettings").woocommerce_default_date_range;return{summaryData:Object(w.e)({endpoint:a,query:o,select:e,limitBy:s,filters:c,advancedFilters:i,defaultDateRange:d,fields:l}),defaultDateRange:d}})))(C)},772:function(e,t,r){"use strict";r.d(t,"b",(function(){return m})),r.d(t,"c",(function(){return p})),r.d(t,"a",(function(){return f}));var a,n,o=r(16),c=r.n(o),i=r(37),s=r.n(i),u=r(3),l=r(44),d=r(740),m=Object(l.applyFilters)("woocommerce_admin_downloads_report_charts",[{key:"download_count",label:Object(u.__)("Downloads",'woocommerce'),type:"number"}]),p=Object(l.applyFilters)("woocommerce_admin_downloads_report_filters",[{label:Object(u.__)("Show",'woocommerce'),staticParams:["chartType","paged","per_page"],param:"filter",showFilters:function(){return!0},filters:[{label:Object(u.__)("All Downloads",'woocommerce'),value:"all"},{label:Object(u.__)("Advanced Filters",'woocommerce'),value:"advanced"}]}]),f=Object(l.applyFilters)("woocommerce_admin_downloads_report_advanced_filters",{title:Object(u._x)("Downloads Match {{select /}} Filters","A sentence describing filters for Downloads. See screen shot for context: https://cloudup.com/ccxhyH2mEDg",'woocommerce'),filters:{product:{labels:{add:Object(u.__)("Product",'woocommerce'),placeholder:Object(u.__)("Search",'woocommerce'),remove:Object(u.__)("Remove product filter",'woocommerce'),rule:Object(u.__)("Select a product filter match",'woocommerce'),title:Object(u.__)("{{title}}Product{{/title}} {{rule /}} {{filter /}}",'woocommerce'),filter:Object(u.__)("Select product",'woocommerce')},rules:[{value:"includes",label:Object(u._x)("Includes","products",'woocommerce')},{value:"excludes",label:Object(u._x)("Excludes","products",'woocommerce')}],input:{component:"Search",type:"products",getLabels:d.d}},customer:{labels:{add:Object(u.__)("Username",'woocommerce'),placeholder:Object(u.__)("Search customer username",'woocommerce'),remove:Object(u.__)("Remove customer username filter",'woocommerce'),rule:Object(u.__)("Select a customer username filter match",'woocommerce'),title:Object(u.__)("{{title}}Username{{/title}} {{rule /}} {{filter /}}",'woocommerce'),filter:Object(u.__)("Select customer username",'woocommerce')},rules:[{value:"includes",label:Object(u._x)("Includes","customer usernames",'woocommerce')},{value:"excludes",label:Object(u._x)("Excludes","customer usernames",'woocommerce')}],input:{component:"Search",type:"usernames",getLabels:d.c}},order:{labels:{add:Object(u.__)("Order #",'woocommerce'),placeholder:Object(u.__)("Search order number",'woocommerce'),remove:Object(u.__)("Remove order number filter",'woocommerce'),rule:Object(u.__)("Select a order number filter match",'woocommerce'),title:Object(u.__)("{{title}}Order #{{/title}} {{rule /}} {{filter /}}",'woocommerce'),filter:Object(u.__)("Select order number",'woocommerce')},rules:[{value:"includes",label:Object(u._x)("Includes","order numbers",'woocommerce')},{value:"excludes",label:Object(u._x)("Excludes","order numbers",'woocommerce')}],input:{component:"Search",type:"orders",getLabels:(n=s()(c.a.mark((function e(t){var r;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return r=t.split(","),e.next=3,r.map((function(e){return{id:e,label:"#"+e}}));case 3:return e.abrupt("return",e.sent);case 4:case"end":return e.stop()}}),e)}))),function(e){return n.apply(this,arguments)})}},ip_address:{labels:{add:Object(u.__)("IP Address",'woocommerce'),placeholder:Object(u.__)("Search IP address",'woocommerce'),remove:Object(u.__)("Remove IP address filter",'woocommerce'),rule:Object(u.__)("Select an IP address filter match",'woocommerce'),title:Object(u.__)("{{title}}IP Address{{/title}} {{rule /}} {{filter /}}",'woocommerce'),filter:Object(u.__)("Select IP address",'woocommerce')},rules:[{value:"includes",label:Object(u._x)("Includes","IP addresses",'woocommerce')},{value:"excludes",label:Object(u._x)("Excludes","IP addresses",'woocommerce')}],input:{component:"Search",type:"downloadIps",getLabels:(a=s()(c.a.mark((function e(t){var r;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return r=t.split(","),e.next=3,r.map((function(e){return{id:e,label:e}}));case 3:return e.abrupt("return",e.sent);case 4:case"end":return e.stop()}}),e)}))),function(e){return a.apply(this,arguments)})}}}})}}]);