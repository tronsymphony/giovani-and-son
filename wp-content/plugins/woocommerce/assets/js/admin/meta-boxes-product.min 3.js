jQuery(function(n){function i(){var t=n("select#product-type").val(),e=n("input#_virtual:checked").length,i=n("input#_downloadable:checked").length,o=".hide_if_downloadable, .hide_if_virtual",a=".show_if_downloadable, .show_if_virtual";n.each(woocommerce_admin_meta_boxes.product_types,function(t,e){o=o+", .hide_if_"+e,a=a+", .show_if_"+e}),n(o).show(),n(a).hide(),i&&n(".show_if_downloadable").show(),e&&n(".show_if_virtual").show(),n(".show_if_"+t).show(),i&&n(".hide_if_downloadable").hide(),e&&n(".hide_if_virtual").hide(),n(".hide_if_"+t).hide(),n("input#_manage_stock").change(),n(".woocommerce_options_panel").each(function(){var t,e=n(this).children(".options_group");0!==e.length&&e.filter(function(){return"none"===n(this).css("display")}).length===e.length&&(t=n(this).prop("id"),n(".product_data_tabs").find('li a[href="#'+t+'"]').parent().hide())})}function t(t){var e=n(t).next().is(".hasDatepicker")?"minDate":"maxDate",i="minDate"==e?n(t).next():n(t).prev(),o=n(t).datepicker("getDate");n(i).datepicker("option",e,o),n(t).change()}n(function(){n('[id$="-all"] > ul.categorychecklist').each(function(){var t,e,i=n(this),o=i.find(":checked").first();o.length&&(t=i.find("input").position().top,e=o.position().top,i.closest(".tabs-panel").scrollTop(e-t+5))})}),n("#upsell_product_data").bind("keypress",function(t){if(13===t.keyCode)return!1}),n("body").hasClass("wc-wp-version-gte-55")?n(".type_box").appendTo("#woocommerce-product-data .hndle"):n(".type_box").appendTo("#woocommerce-product-data .hndle span"),n(function(){n("#woocommerce-product-data").find(".hndle").unbind("click.postboxes"),n("#woocommerce-product-data").on("click",".hndle",function(t){n(t.target).filter("input, option, label, select").length||n("#woocommerce-product-data").toggleClass("closed")})}),n("#catalog-visibility").find(".edit-catalog-visibility").click(function(){return n("#catalog-visibility-select").is(":hidden")&&(n("#catalog-visibility-select").slideDown("fast"),n(this).hide()),!1}),n("#catalog-visibility").find(".save-post-visibility").click(function(){n("#catalog-visibility-select").slideUp("fast"),n("#catalog-visibility").find(".edit-catalog-visibility").show();var t=n("input[name=_visibility]:checked").attr("data-label");return n("input[name=_featured]").is(":checked")&&(t=t+", "+woocommerce_admin_meta_boxes.featured_label,n("input[name=_featured]").attr("checked","checked")),n("#catalog-visibility-display").text(t),!1}),n("#catalog-visibility").find(".cancel-post-visibility").click(function(){n("#catalog-visibility-select").slideUp("fast"),n("#catalog-visibility").find(".edit-catalog-visibility").show();var t=n("#current_visibility").val(),e=n("#current_featured").val();n("input[name=_visibility]").removeAttr("checked"),n("input[name=_visibility][value="+t+"]").attr("checked","checked");var i=n("input[name=_visibility]:checked").attr("data-label");return"yes"===e?(i=i+", "+woocommerce_admin_meta_boxes.featured_label,n("input[name=_featured]").attr("checked","checked")):n("input[name=_featured]").removeAttr("checked"),n("#catalog-visibility-display").text(i),!1}),n("select#product-type").change(function(){var t=n(this).val();"variable"===t?(n("input#_manage_stock").change(),n("input#_downloadable").prop("checked",!1),n("input#_virtual").removeAttr("checked")):"grouped"!==t&&"external"!==t||(n("input#_downloadable").prop("checked",!1),n("input#_virtual").removeAttr("checked")),i(),n("ul.wc-tabs li:visible").eq(0).find("a").click(),n(document.body).trigger("woocommerce-product-type-change",t,n(this))}).change(),n("input#_downloadable, input#_virtual").change(function(){i()}),n(".sale_price_dates_fields").each(function(){var t=n(this),e=!1,i=t.closest("div, table");t.find("input").each(function(){""!==n(this).val()&&(e=!0)}),e?(i.find(".sale_schedule").hide(),i.find(".sale_price_dates_fields").show()):(i.find(".sale_schedule").show(),i.find(".sale_price_dates_fields").hide())}),n("#woocommerce-product-data").on("click",".sale_schedule",function(){var t=n(this).closest("div, table");return n(this).hide(),t.find(".cancel_sale_schedule").show(),t.find(".sale_price_dates_fields").show(),!1}),n("#woocommerce-product-data").on("click",".cancel_sale_schedule",function(){var t=n(this).closest("div, table");return n(this).hide(),t.find(".sale_schedule").show(),t.find(".sale_price_dates_fields").hide(),t.find(".sale_price_dates_fields").find("input").val(""),!1}),n("#woocommerce-product-data").on("click",".downloadable_files a.insert",function(){return n(this).closest(".downloadable_files").find("tbody").append(n(this).data("row")),!1}),n("#woocommerce-product-data").on("click",".downloadable_files a.delete",function(){return n(this).closest("tr").remove(),!1}),n("input#_manage_stock").change(function(){var t;n(this).is(":checked")?(n("div.stock_fields").show(),n("p.stock_status_field").hide()):(t=n("select#product-type").val(),n("div.stock_fields").hide(),n("p.stock_status_field:not( .hide_if_"+t+" )").show()),n("input.variable_manage_stock").change()}).change(),n(".sale_price_dates_fields").each(function(){n(this).find("input").datepicker({defaultDate:"",dateFormat:"yy-mm-dd",numberOfMonths:1,showButtonPanel:!0,onSelect:function(){t(n(this))}}),n(this).find("input").each(function(){t(n(this))})});var o,a,e,c=n(".product_attributes").find(".woocommerce_attribute").get();function r(){n(".product_attributes .woocommerce_attribute").each(function(t,e){n(".attribute_position",e).val(parseInt(n(e).index(".product_attributes .woocommerce_attribute"),10))})}c.sort(function(t,e){var i=parseInt(n(t).attr("rel"),10),o=parseInt(n(e).attr("rel"),10);return i<o?-1:o<i?1:0}),n(c).each(function(t,e){n(".product_attributes").append(e)}),n(".product_attributes .woocommerce_attribute").each(function(t,e){"none"!==n(e).css("display")&&n(e).is(".taxonomy")&&n("select.attribute_taxonomy").find('option[value="'+n(e).data("taxonomy")+'"]').attr("disabled","disabled")}),n("button.add_attribute").on("click",function(){var t=n(".product_attributes .woocommerce_attribute").length,e=n("select.attribute_taxonomy").val(),i=n(this).closest("#product_attributes"),o=i.find(".product_attributes"),a=n("select#product-type").val(),c={action:"woocommerce_add_attribute",taxonomy:e,i:t,security:woocommerce_admin_meta_boxes.add_attribute_nonce};return i.block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),n.post(woocommerce_admin_meta_boxes.ajax_url,c,function(t){o.append(t),"variable"!==a&&o.find(".enable_variation").hide(),n(document.body).trigger("wc-enhanced-select-init"),r(),o.find(".woocommerce_attribute").last().find("h3").click(),i.unblock(),n(document.body).trigger("woocommerce_added_attribute")}),e&&(n("select.attribute_taxonomy").find('option[value="'+e+'"]').attr("disabled","disabled"),n("select.attribute_taxonomy").val("")),!1}),n(".product_attributes").on("blur","input.attribute_name",function(){n(this).closest(".woocommerce_attribute").find("strong.attribute_name").text(n(this).val())}),n(".product_attributes").on("click","button.select_all_attributes",function(){return n(this).closest("td").find("select option").attr("selected","selected"),n(this).closest("td").find("select").change(),!1}),n(".product_attributes").on("click","button.select_no_attributes",function(){return n(this).closest("td").find("select option").removeAttr("selected"),n(this).closest("td").find("select").change(),!1}),n(".product_attributes").on("click",".remove_row",function(){var t;return window.confirm(woocommerce_admin_meta_boxes.remove_attribute)&&((t=n(this).parent().parent()).is(".taxonomy")?(t.find("select, input[type=text]").val(""),t.hide(),n("select.attribute_taxonomy").find('option[value="'+t.data("taxonomy")+'"]').removeAttr("disabled")):(t.find("select, input[type=text]").val(""),t.hide(),r())),!1}),n(".product_attributes").sortable({items:".woocommerce_attribute",cursor:"move",axis:"y",handle:"h3",scrollSensitivity:40,forcePlaceholderSize:!0,helper:"clone",opacity:.65,placeholder:"wc-metabox-sortable-placeholder",start:function(t,e){e.item.css("background-color","#f6f6f6")},stop:function(t,e){e.item.removeAttr("style"),r()}}),n(".product_attributes").on("click","button.add_new_attribute",function(){n(".product_attributes").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});var t,e=n(this).closest(".woocommerce_attribute"),i=e.data("taxonomy"),o=window.prompt(woocommerce_admin_meta_boxes.new_attribute_prompt);return o?(t={action:"woocommerce_add_new_attribute",taxonomy:i,term:o,security:woocommerce_admin_meta_boxes.add_attribute_nonce},n.post(woocommerce_admin_meta_boxes.ajax_url,t,function(t){t.error?window.alert(t.error):t.slug&&(e.find("select.attribute_values").append('<option value="'+t.term_id+'" selected="selected">'+t.name+"</option>"),e.find("select.attribute_values").change()),n(".product_attributes").unblock()})):n(".product_attributes").unblock(),!1}),n(".save_attributes").on("click",function(){n(".product_attributes").block({message:null,overlayCSS:{background:"#fff",opacity:.6}});var t=n(".product_attributes").find("input, select, textarea"),e={post_id:woocommerce_admin_meta_boxes.post_id,product_type:n("#product-type").val(),data:t.serialize(),action:"woocommerce_save_attributes",security:woocommerce_admin_meta_boxes.save_attributes_nonce};n.post(woocommerce_admin_meta_boxes.ajax_url,e,function(t){var e;t.error?window.alert(t.error):t.data&&(n(".product_attributes").html(t.data.html),n(".product_attributes").unblock(),i(),n("select.attribute_taxonomy").find("option").prop("disabled",!1),n(".product_attributes .woocommerce_attribute").each(function(t,e){"none"!==n(e).css("display")&&n(e).is(".taxonomy")&&n("select.attribute_taxonomy").find('option[value="'+n(e).data("taxonomy")+'"]').prop("disabled",!0)}),e=(e=window.location.toString()).replace("post-new.php?","post.php?post="+woocommerce_admin_meta_boxes.post_id+"&action=edit&"),n("#variable_product_options").load(e+" #variable_product_options_inner",function(){n("#variable_product_options").trigger("reload")}))})}),n(document.body).on("click",".upload_file_button",function(t){var e,i=n(this);a=i.closest("tr").find("td.file_url input"),t.preventDefault(),o||(e=[new wp.media.controller.Library({library:wp.media.query(),multiple:!0,title:i.data("choose"),priority:20,filterable:"uploaded"})],(o=wp.media.frames.downloadable_file=wp.media({title:i.data("choose"),library:{type:""},button:{text:i.data("update")},multiple:!0,states:e})).on("select",function(){var e="";o.state().get("selection").map(function(t){(t=t.toJSON()).url&&(e=t.url)}),a.val(e).change()}),o.on("ready",function(){o.uploader.options.uploader.params={type:"downloadable_product"}})),o.open()}),n(".downloadable_files tbody").sortable({items:"tr",cursor:"move",axis:"y",handle:"td.sort",scrollSensitivity:40,forcePlaceholderSize:!0,helper:"clone",opacity:.65});var l=n("#product_image_gallery"),s=n("#product_images_container").find("ul.product_images");n(".add_product_images").on("click","a",function(t){var o=n(this);t.preventDefault(),e||(e=wp.media.frames.product_gallery=wp.media({title:o.data("choose"),button:{text:o.data("update")},states:[new wp.media.controller.Library({title:o.data("choose"),filterable:"all",multiple:!0})]})).on("select",function(){var t=e.state().get("selection"),i=l.val();t.map(function(t){var e;(t=t.toJSON()).id&&(i=i?i+","+t.id:t.id,e=t.sizes&&t.sizes.thumbnail?t.sizes.thumbnail.url:t.url,s.append('<li class="image" data-attachment_id="'+t.id+'"><img src="'+e+'" /><ul class="actions"><li><a href="#" class="delete" title="'+o.data("delete")+'">'+o.data("text")+"</a></li></ul></li>"))}),l.val(i)}),e.open()}),s.sortable({items:"li.image",cursor:"move",scrollSensitivity:40,forcePlaceholderSize:!0,forceHelperSize:!1,helper:"clone",opacity:.65,placeholder:"wc-metabox-sortable-placeholder",start:function(t,e){e.item.css("background-color","#f6f6f6")},stop:function(t,e){e.item.removeAttr("style")},update:function(){var e="";n("#product_images_container").find("ul li.image").css("cursor","default").each(function(){var t=n(this).attr("data-attachment_id");e=e+t+","}),l.val(e)}}),n("#product_images_container").on("click","a.delete",function(){n(this).closest("li.image").remove();var e="";return n("#product_images_container").find("ul li.image").css("cursor","default").each(function(){var t=n(this).attr("data-attachment_id");e=e+t+","}),l.val(e),n("#tiptip_holder").removeAttr("style"),n("#tiptip_arrow").removeAttr("style"),!1})});