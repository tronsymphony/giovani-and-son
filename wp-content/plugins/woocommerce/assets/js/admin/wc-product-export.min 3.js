!function(n,i){var o=function(o){this.$form=o,this.xhr=!1,this.$form.find(".woocommerce-exporter-progress").val(0),this.processStep=this.processStep.bind(this),o.on("submit",{productExportForm:this},this.onSubmit),o.find(".woocommerce-exporter-types").on("change",{productExportForm:this},this.exportTypeFields)};o.prototype.onSubmit=function(o){o.preventDefault();var e=new Date,r="wc-product-export-"+e.getDate()+"-"+(e.getMonth()+1)+"-"+e.getFullYear()+"-"+e.getTime()+".csv";o.data.productExportForm.$form.addClass("woocommerce-exporter__exporting"),o.data.productExportForm.$form.find(".woocommerce-exporter-progress").val(0),o.data.productExportForm.$form.find(".woocommerce-exporter-button").prop("disabled",!0),o.data.productExportForm.processStep(1,n(this).serialize(),"",r)},o.prototype.processStep=function(o,e,r,t){var c=this,p=n(".woocommerce-exporter-columns").val(),a=n("#woocommerce-exporter-meta:checked").length?1:0,s=n(".woocommerce-exporter-types").val(),m=n(".woocommerce-exporter-category").val();n.ajax({type:"POST",url:ajaxurl,data:{form:e,action:"woocommerce_do_ajax_product_export",step:o,columns:r,selected_columns:p,export_meta:a,export_types:s,export_category:m,filename:t,security:wc_product_export_params.export_nonce},dataType:"json",success:function(o){o.success&&("done"===o.data.step?(c.$form.find(".woocommerce-exporter-progress").val(o.data.percentage),i.location=o.data.url,setTimeout(function(){c.$form.removeClass("woocommerce-exporter__exporting"),c.$form.find(".woocommerce-exporter-button").prop("disabled",!1)},2e3)):(c.$form.find(".woocommerce-exporter-progress").val(o.data.percentage),c.processStep(parseInt(o.data.step,10),e,o.data.columns,t)))}}).fail(function(o){i.console.log(o)})},o.prototype.exportTypeFields=function(){var o=n(".woocommerce-exporter-category");-1!==n.inArray("variation",n(this).val())?(o.closest("tr").hide(),o.val("").change()):o.closest("tr").show()},n.fn.wc_product_export_form=function(){return new o(this),this},n(".woocommerce-exporter").wc_product_export_form()}(jQuery,window);