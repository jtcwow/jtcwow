jQuery(document).ready(function(e){function t(){var t=280;e(".wcfmmp-single-store").hasClass("coloum-2")&&(e(".wcfmmp-single-store .store-footer").each(function(){e(this).outerHeight()>t&&(t=e(this).outerHeight())}),e(".wcfmmp-single-store .store-footer").css("height",t)),e(".wcfmmp-store-lists-sorting #wcfmmp_store_orderby").on("change",function(){e(this).parent().submit()})}$current_location_fetched=!1,e("#wcfmmp-stores-lists").parent().hasClass("col-md-8")&&(e("#wcfmmp-stores-lists").parent().removeClass("col-md-8"),e("#wcfmmp-stores-lists").parent().addClass("col-md-12")),e("#wcfmmp-stores-lists").parent().hasClass("col-md-9")&&(e("#wcfmmp-stores-lists").parent().removeClass("col-md-9"),e("#wcfmmp-stores-lists").parent().addClass("col-md-12")),e("#wcfmmp-stores-lists").parent().hasClass("col-sm-8")&&(e("#wcfmmp-stores-lists").parent().removeClass("col-sm-8"),e("#wcfmmp-stores-lists").parent().addClass("col-md-12")),e("#wcfmmp-stores-lists").parent().hasClass("col-sm-9")&&(e("#wcfmmp-stores-lists").parent().removeClass("col-sm-9"),e("#wcfmmp-stores-lists").parent().addClass("col-md-12")),e("#wcfmmp-stores-lists").parent().removeClass("col-sm-push-3"),e("#wcfmmp-stores-lists").parent().removeClass("col-sm-push-4"),e("#wcfmmp-stores-lists").parent().removeClass("col-md-push-3"),e("#wcfmmp-stores-lists").parent().removeClass("col-md-push-4"),e(".left_sidebar").length>0&&e(window).width()>768&&($left_sidebar_height=e(".left_sidebar").outerHeight(),$right_side_height=e(".right_side").outerHeight(),$left_sidebar_height<$right_side_height&&e(".left_sidebar").css("height",$right_side_height+50)),setTimeout(function(){t()},200),e("#wcfmmp_store_country").length>0&&e("#wcfmmp_store_country").select2({allowClear:!0,placeholder:wcfmmp_store_list_messages.choose_location+" ..."}),e("#wcfmmp_store_category").length>0&&e("#wcfmmp_store_category").select2({allowClear:!0,placeholder:wcfmmp_store_list_messages.choose_category+" ..."}),e(".wcfm-custom-search-select-field").length>0&&e(".wcfm-custom-search-select-field").each(function(){$title=e(this).data("title"),e(this).select2({allowClear:!0,placeholder:$title+" ..."})});var s,o=e(".wcfmmp-store-search-form"),a=null;function r(){data={action:"wcfmmp_stores_list_search",pagination_base:o.find("#pagination_base").val(),paged:o.find("#wcfm_paged").val(),per_row:$per_row,per_page:$per_page,includes:$includes,excludes:$excludes,orderby:e("#wcfmmp_store_orderby").val(),has_orderby:$has_orderby,has_product:$has_product,sidebar:$sidebar,theme:$theme,search_term:e(".wcfmmp-store-search").val(),wcfmmp_store_category:e("#wcfmmp_store_category").val(),search_data:jQuery(".wcfmmp-store-search-form").serialize(),_wpnonce:o.find("#nonce").val()},a&&clearTimeout(a),s&&s.abort(),a=setTimeout(function(){e(".wcfmmp-stores-listing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),s=e.post(wcfm_params.ajax_url,data,function(s){if(s.success){e(".wcfmmp-stores-listing").unblock();var o=s.data;e("#wcfmmp-stores-wrap").html(e(o).find(".wcfmmp-stores-content")),f(),initiateTip(),e(".wcfm_catalog_enquiry").each(function(){e(this).hasClass("wcfm_login_popup")?jQuery(".wcfm_login_popup").each(function(){jQuery(this).click(function(e){return e.preventDefault(),jQuerylogin_popup=jQuery(this),jQuery("body").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),jQuery.ajax({type:"POST",url:wcfm_params.ajax_url,data:{action:"wcfm_login_popup_form"},success:function(e){jQuery.colorbox({html:e,width:$popup_width,onComplete:function(){jQuery("#wcfm_login_popup_button").click(function(){if($wcfm_is_valid_form=!0,jQuery("#wcfm_login_popup_form").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),jQuery(".wcfm-message").html("").removeClass("wcfm-error").removeClass("wcfm-success").slideUp(),0==jQuery("input[name=wcfm_login_popup_username]").val().length)jQuery("#wcfm_login_popup_form .wcfm-message").html('<span class="wcicon-status-cancelled"></span>'+wcfm_login_messages.no_username).addClass("wcfm-error").slideDown(),wcfm_notification_sound.play(),jQuery("#wcfm_login_popup_form").unblock();else if(0==jQuery("input[name=wcfm_login_popup_password]").val().length)jQuery("#wcfm_login_popup_form .wcfm-message").html('<span class="wcicon-status-cancelled"></span>'+wcfm_login_messages.no_password).addClass("wcfm-error").slideDown(),wcfm_notification_sound.play(),jQuery("#wcfm_login_popup_form").unblock();else if(jQuery(document.body).trigger("wcfm_form_validate",jQuery("#wcfm_login_popup_form")),$wcfm_is_valid_form){jQuery("#wcfm_login_popup_button").hide();var e={action:"wcfm_login_popup_submit",wcfm_login_popup_form:jQuery("#wcfm_login_popup_form").serialize()};jQuery.post(wcfm_params.ajax_url,e,function(e){e&&(jQueryresponse_json=jQuery.parseJSON(e),wcfm_notification_sound.play(),jQuery(".wcfm-message").html("").removeClass("wcfm-error").removeClass("wcfm-success").slideUp(),jQueryresponse_json.status?(jQuery("#wcfm_login_popup_form .wcfm-message").html('<span class="wcicon-status-completed"></span>'+jQueryresponse_json.message).addClass("wcfm-success").slideDown(),window.location=window.location.href):(jQuery("#wcfm_login_popup_form .wcfm-message").html('<span class="wcicon-status-cancelled"></span>'+jQueryresponse_json.message).addClass("wcfm-error").slideDown(),jQuery("#wcfm_login_popup_button").show(),jQuery("#wcfm_login_popup_form").unblock()))})}else wcfm_notification_sound.play(),jQuery("#wcfm_login_popup_form").unblock()})}}),jQuery("body").unblock()}}),!1})}):e(this).off("click").on("click",function(t){t.preventDefault(),$store=e(this).data("store"),$product=e(this).data("product"),e.colorbox({inline:!0,href:"#enquiry_form_wrapper",width:$popup_width,onComplete:function(){e("#wcfm_enquiry_form").find("#enquiry_vendor_id").val($store),e("#wcfm_enquiry_form").find("#enquiry_product_id").val($product),jQuery(".anr_captcha_field").length>0&&"undefined"!=typeof grecaptcha&&($wcfm_anr_loaded?grecaptcha.reset():wcfm_anr_onloadCallback(),$wcfm_anr_loaded=!0)}})})}),setTimeout(function(){t()},200)}})},500)}if($wcfm_anr_loaded=!1,e(".wcfmmp-store-search-form").length>0){wcfmmp_store_list_options.is_geolocate&&(0!=e("#wcfmmp_radius_addr").length&&navigator.geolocation||r()),o.on("keyup",".wcfm-search-field",function(){r()}),o.on("keyup","#search",function(){r()}),e(".wcfm-search-field").on("input",function(e){r()}),e("#search").on("input",function(e){r()}),e(".wcfm-custom-search-input-field").on("input",function(e){r()}),o.on("change","#wcfmmp_store_category",function(){r()}),o.on("change",".wcfm-custom-search-select-field",function(){r()}),e(document.body).on("wcfm_store_list_country_changed",function(e){r()}),o.on("change","#wcfmmp_store_state",function(){r()}),o.on("keyup","#wcfmmp_store_state",function(){r()});var c="";if(e("#wcfmmp_radius_addr").length>0){var m=parseInt(wcfmmp_store_list_options.max_radius),i=document.getElementById("wcfmmp_radius_addr");if("google"==wcfm_maps.lib){var n=new google.maps.Geocoder,l=new google.maps.places.Autocomplete(i);l.addListener("place_changed",function(){var t=l.getPlace();e("#wcfmmp_radius_lat").val(t.geometry.location.lat()),e("#wcfmmp_radius_lng").val(t.geometry.location.lng()),r()}),e("#wcfmmp_radius_addr").blur(function(){0==e(this).val().length&&(e("#wcfmmp_radius_lat").val(""),e("#wcfmmp_radius_lng").val(""),r())})}else c=new L.Control.Search({container:"wcfm_radius_filter_container",url:"https://nominatim.openstreetmap.org/search?format=json&q={s}",jsonpParam:"json_callback",propertyName:"display_name",propertyLoc:["lat","lon"],marker:L.marker([0,0]),moveToLocation:function(t,s,o){e("#wcfmmp_radius_lat").val(t.lat),e("#wcfmmp_radius_lng").val(t.lng),r()},initial:!1,collapsed:!1,autoType:!1,minLength:2});if(e("#wcfmmp_radius_range").on("input",function(){e(".wcfmmp_radius_range_cur").html(this.value+wcfmmp_store_list_options.radius_unit),wcfmmp_store_list_options.is_rtl?e(".wcfmmp_radius_range_cur").css("right",this.value/m*e(".wcfm_radius_slidecontainer").outerWidth()-7.5+"px"):e(".wcfmmp_radius_range_cur").css("left",this.value/m*e(".wcfm_radius_slidecontainer").outerWidth()-7.5+"px"),$wcfmmp_radius_lat=e("#wcfmmp_radius_lat").val(),$wcfmmp_radius_lat&&setTimeout(function(){r()},100)}),wcfmmp_store_list_options.is_rtl?e(".wcfmmp_radius_range_cur").css("right",e("#wcfmmp_radius_range").val()/m*e(".wcfm_radius_slidecontainer").outerWidth()-7.5+"px"):e(".wcfmmp_radius_range_cur").css("left",e("#wcfmmp_radius_range").val()/m*e(".wcfm_radius_slidecontainer").outerWidth()-7.5+"px"),navigator.geolocation){e(".wcfmmmp_locate_icon").on("click",function(){navigator.geolocation.getCurrentPosition(function(t){$current_location_fetched=!0,console.log(t.coords.latitude,t.coords.longitude),"google"==wcfm_maps.lib?n.geocode({location:{lat:t.coords.latitude,lng:t.coords.longitude}},function(s,o){"OK"===o&&(e("#wcfmmp_radius_addr").val(s[0].formatted_address),e("#wcfmmp_radius_lat").val(t.coords.latitude),e("#wcfmmp_radius_lng").val(t.coords.longitude),r())}):e.get("https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat="+t.coords.latitude+"&lon="+t.coords.longitude,function(s){e("#wcfmmp_radius_addr").val(s.address.road),e("#wcfmmp_radius_lat").val(t.coords.latitude),e("#wcfmmp_radius_lng").val(t.coords.longitude),r()})})}),wcfmmp_store_list_options.is_geolocate&&e(".wcfmmmp_locate_icon").click()}}}var _=e(".wcfmmp-store-search-form"),p="";function f(){if(e(".wcfmmp-store-list-map").length>0){!function(){if("google"==wcfm_maps.lib)for(var e=0;e<u.length;e++)u[e].setMap(null);u=[]}();var t={action:"wcfmmp_stores_list_map_markers",pagination_base:o.find("#pagination_base").val(),paged:o.find("#wcfm_paged").val(),per_row:$per_row,per_page:$per_page,includes:$includes,excludes:$excludes,has_product:$has_product,has_orderby:$has_orderby,sidebar:$sidebar,theme:$theme,search_term:e(".wcfmmp-store-search").val(),wcfmmp_store_category:e("#wcfmmp_store_category").val(),wcfmmp_store_country:e("#wcfmmp_store_country").val(),wcfmmp_store_state:e("#wcfmmp_store_state").val(),search_data:jQuery(".wcfmmp-store-search-form").serialize()};s=e.post(wcfm_params.ajax_url,t,function(t){if(t.success){var s=t.data;!function(t){if($icon_width=parseInt(wcfmmp_store_list_options.icon_width),$icon_height=parseInt(wcfmmp_store_list_options.icon_height),"google"==wcfm_maps.lib){var s=new google.maps.LatLngBounds,o=new google.maps.InfoWindow;if(e.each(t,function(e,t){var a=new google.maps.LatLng(t.lat,t.lang);s.extend(a);var r={url:t.icon,scaledSize:new google.maps.Size($icon_width,$icon_height)},c=new google.maps.Marker({position:a,map:d,animation:google.maps.Animation.DROP,title:t.name,icon:r,zIndex:e}),m=t.info_window_content;google.maps.event.addListener(c,"click",function(e,t){return function(){o.setContent(m),o.open(d,e)}}(c)),d.setCenter(c.getPosition()),u.push(c)}),wcfmmp_store_list_options.is_cluster){const e=wcfmmp_store_list_options.cluster_image;markerClusterer&&markerClusterer.clearMarkers(),markerClusterer=new MarkerClusterer(d,u,{imagePath:e})}$auto_zoom&&t.length>0&&d.fitBounds(s)}else markersGroup&&markersGroup.clearLayers(),e.each(t,function(e,s){var o=L.icon({iconUrl:s.icon,iconSize:[$icon_width,$icon_height]}),a=L.marker([s.lat,s.lang],{icon:o}).bindPopup(s.info_window_content);u.push(a),markersGroup=L.featureGroup(u).addTo(d),$auto_zoom&&t.length>0&&setTimeout(function(){d.fitBounds(markersGroup.getBounds())},1e3)})}(e.parseJSON(s))}})}}if({init:function(){_.on("change","select#wcfmmp_store_country",this.state_select)},state_select:function(){var t=wc_country_select_params.countries.replace(/&quot;/g,'"'),s=e.parseJSON(t),o=e("#wcfmmp_store_state"),a=o.val(),r=e(this).val();o.data("required");if(s[r])if(e.isEmptyObject(s[r]))o.is("select")&&e("select#wcfmmp_store_state").replaceWith('<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+wcfmmp_store_list_messages.choose_state+' ..." />'),a?e("#wcfmmp_store_state").val(a):e("#wcfmmp_store_state").val("");else{p="";var c="",m=s[r];for(var i in m){var n;if(m.hasOwnProperty(i))c=c+'<option value="'+i+'"'+n+">"+m[i]+"</option>"}o.is("select")&&e("select#wcfmmp_store_state").html('<option value="">'+wcfmmp_store_list_messages.choose_state+" ...</option>"+c),o.is("input")&&(e("input#wcfmmp_store_state").replaceWith('<select class="wcfm-select wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state"></select>'),e("select#wcfmmp_store_state").html('<option value="">'+wcfmmp_store_list_messages.choose_state+" ...</option>"+c))}else o.is("select")&&e("select#wcfmmp_store_state").replaceWith('<input type="text" class="wcfm-text wcfm_ele" name="wcfmmp_store_state" id="wcfmmp_store_state" placeholder="'+wcfmmp_store_list_messages.choose_state+' ..." />'),e("#wcfmmp_store_state").val(p),"N/A"==e("#wcfmmp_store_state").val()&&e("#wcfmmp_store_state").val("");e(document.body).trigger("wcfm_store_list_country_changed")}}.init(),e(".wcfmmp-store-list-map").length>0){e(".wcfmmp-store-list-map").css("height",e(".wcfmmp-store-list-map").outerWidth()/2);var u=[],d=markerClusterer=markersGroup="";if(wcfmmp_store_list_options.is_poi)w=[];else var w=[{featureType:"poi",elementType:"labels",stylers:[{visibility:"off"}]}];if("google"==wcfm_maps.lib){var g={zoom:$map_zoom,center:new google.maps.LatLng(wcfmmp_store_list_options.default_lat,wcfmmp_store_list_options.default_lng,13),mapTypeId:wcfm_maps.map_type,styles:w};d=new google.maps.Map(document.getElementById("wcfmmp-store-list-map"),g)}else d=L.map("wcfmmp-store-list-map",{center:[wcfmmp_store_list_options.default_lat,wcfmmp_store_list_options.default_lng],minZoom:2,zoom:$map_zoom,zoomAnimation:!1}),wcfmmp_store_list_options.is_allow_scroll_zoom||d.scrollWheelZoom.disable(),L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{subdomains:["a","b","c"]}).addTo(d),c&&e("#wcfmmp_radius_addr").length>0&&(e("#wcfmmp_radius_addr").remove(),d.addControl(c),e("#wcfm_radius_filter_container").find(".search-input").addClass("wcfmmp-radius-addr").attr("id","wcfmmp_radius_addr").css("float","none").attr("placeholder",wcfmmp_store_list_options.search_location));wcfmmp_store_list_options.is_geolocate&&$current_location_fetched||f()}});