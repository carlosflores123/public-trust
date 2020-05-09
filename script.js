jQuery(document).ready(function($){
	var selected_team = 43;
	var selected_location = 38;

	$(".select_team_form").change(function(){
		selected_team = $(this).children("option:selected").val();
		selected_location = $('.select_location_form').children("option:selected").val();
		flag = 0;
		get_member_by_ajax(selected_team, selected_location, flag);		

		setTimeout(function(){ 
			$team_modal = $('.team_member_modal');
			$span_close = $('.team_member_modal span.close_icon');

		    $('.team-member').click(function(){
				$member_dev = $(this);
				member_bio_modal($member_dev);
			})

			$span_close.click(function(event){
				$team_modal.css('display', 'none');
			})

		}, 3000);		

	})	

	$(".select_location_form").change(function(){
		selected_location = $(this).children("option:selected").val();
		selected_team = $('.select_team_form').children("option:selected").val();
		flag = 1;
		get_member_by_ajax(selected_team, selected_location, flag);

		setTimeout(function(){ 
			$team_modal = $('.team_member_modal');
			$span_close = $('.team_member_modal span.close_icon');

		    $('.team-member').click(function(){
				$member_dev = $(this);
				member_bio_modal($member_dev);
			})

			$span_close.click(function(event){
				$team_modal.css('display', 'none');
			})

		}, 3000);
		
	})
	
	if ($(".elementor-940")[0]){
    	if ($("h2.agenda_header_style")[0]){
    		$('.simple_event_blue_box').css('display', 'none');
		}else{
			$('.event_more_info').css('display', 'none');			
		}
	}

	function get_member_by_ajax(selected_team, selected_location, flag){
		jQuery.ajax({
            type: 'POST',
            url: coolPluginAjaxUrl,
            dataType: 'json',
	        data: {
	            'action'            :   'get_members',
	            'team_number'           :   selected_team,
	            'location_number'           :   selected_location,
	            'flag'           :   flag,
	        },
	        success: function (data) {
	        	
	        	$('.team_member_search .elementor-text-editor').html(data.result['members']);
	        	var locations = data.result['locations'];
	        	console.log(locations);
	        	$location_html = '';
	        	location_count = 0;
	        	for (var key in locations) {
        			location_count += 1;
				}


	        	if(location_count > 1){
	        		$location_html = '<option value="0" selected>All locations</option>';
	        	}

	        	if(flag == 0){
	        		for (var key in locations) {
	        			$location_html += '<option value="' + key + '">' + locations[key] + '</option>';				  
					}

					$('#search_form .selectdiv .select_location_form').html($location_html);
	        	}	        	
	        },
	        error: function (errorThrown) { console.log('error 1'); }
	    });			    
	}

	function member_bio_modal($member_dev){
		$img_url = $member_dev.find("img").attr("src");
		$member_name = $member_dev.find("h5").text();
		$member_title = $member_dev.find("p").text();
		$member_description = $member_dev.find("span").text();
		$('.modal-content .modal-image').html('<img src="' + $img_url + '">');
		$('.modal-content h3.modal-member-name').text($member_name);
		$('.modal-content h5.modal-member-title').text($member_title);
		$member_desc_all = $member_description.split('\n');

		$member_desc_html = '';

		if ($member_desc_all.length == 2) {
			$member_desc_html = '<p>' + $member_desc_all[1] +'</p>'+'<p>'+$member_desc_all[0] + '</p>';
		}else if($member_desc_all.length == 1){
			$member_desc_html = $member_desc_all[0];
		}else{
			$member_desc_html = '<p>' + $member_desc_all[1] +'</p>'+'<p>'+$member_desc_all[0] + '</p>';
			for (var i = $member_desc_all.length - 1; i >= 2; i--) {
				$member_desc_html += '<p>' + $member_desc_all[i] + '</p>';
			}
		}
		
		$('.modal-content .modal-member-description').html($member_desc_html);
		$team_modal.css('display', 'block');

		// $('body').click(function (event) 
		// {
		//    if(!$(event.target).closest('.team_member_modal').length && !$(event.target).is('.team_member_modal')) {
		//      $team_modal.css('display', 'none');
		//    }     
		// });
	}
	
	$team_modal = $('.team_member_modal');
	$span_close = $('.team_member_modal span.close_icon');	

	// if(team_modal.length > 0){	

	// }
	$('.team-member').click(function(){
		$member_dev = $(this);
		member_bio_modal($member_dev);

		$('body').click(function (event) 
		{
			if($team_modal.css('display') == 'block'){
				if($(event.target).closest('.team_member_modal .modal-content, .team-member').length == 0){
					$team_modal.css('display', 'none');
				}
			}	    
		});
	})
	
	$span_close.click(function(event){
		$team_modal.css('display', 'none');		
	})

	// $('.slick-track').slick({	      
	//       prevArrow: $('.custom_prev_btn'),
	//       nextArrow: $('.custom_next_btn'),
	// });

	var $status = $('.paging-info .elementor-text-editor');
	var $slickElement = $('.elementor-element-b7da994');

	$slickElement.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
	  //currentSlide is undefined on init -- set it to 0 in this case (currentSlide is 0 based)
	  var i = (currentSlide ? currentSlide : 0) + 1;
	  $status.text(i + ' of ' + slick.slideCount);
	  if(i == 1){
	  	$('.home-slider .elementor-slick-slider .slick-prev span').css('color', '#e6e6e6');
	  	$('.home-slider .elementor-slick-slider .slick-prev').css('pointer-events', 'none');

	  }else{
	  	$('.home-slider .elementor-slick-slider .slick-prev span').css('color', '#3b6e8f');
	  	$('.home-slider .elementor-slick-slider .slick-prev').css('pointer-events', 'initial');
	  }

	  if(i == slick.slideCount){
	  	$('.home-slider .elementor-slick-slider .slick-next span').css('color', '#e6e6e6');
	  	$('.home-slider .elementor-slick-slider .slick-next').css('pointer-events', 'none');
	  }else{
	  	$('.home-slider .elementor-slick-slider .slick-next span').css('color', '#3b6e8f');
	  	$('.home-slider .elementor-slick-slider .slick-next').css('pointer-events', 'initial');
	  }	  

	});

	if($slickElement.length == 1){
		$slickElement.slick({      
      
		});
	}	

	$win_width = $( window ).width();
	$inner_width = $('.ast-container').width();
	$arrow_left_width = 130;
	$diff_width = 0;
	if($win_width > $inner_width){
		$diff_width = ($win_width - $inner_width)/2;
	};
	$arrow_left_width += $diff_width;
	setTimeout(function(){ 
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-prev').css('left', $arrow_left_width + 'px');
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-next').css('left', $arrow_left_width + 90 + 'px');
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-prev').html("<span>&#10229;</span>");
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-next').html("<span>&#10230;</span>");
	}, 500);

	if($win_width < 768){
		$('.overview-our-service .elementor-tab-title').css('width', ($win_width/2 - 20) + 'px');
		$tab_height = $('.post-36 .elementor-tabs .elementor-tabs-wrapper').height() + 10;
		$('.post-36 .elementor-element.elementor-element-beac539 > .elementor-element-populated').css('margin-top', '-' + $tab_height + 'px');
	}	

	$( window ).resize(function() {
		$win_width = $( window ).width();
		$inner_width = $('.ast-container').width();
		$arrow_left_width = 130;
		$diff_width = 0;
		if($win_width > $inner_width){
			$diff_width = ($win_width - $inner_width)/2;
		};
		$arrow_left_width += $diff_width;

	  	$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-prev').css('left', $arrow_left_width + 'px');
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-next').css('left', $arrow_left_width + 90 + 'px');
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-prev').html("<span>&#10229;</span>");
		$('.home-slider .elementor-slick-slider .slick-arrows-inside .slick-next').html("<span>&#10230;</span>");
		if($win_width < 768){
			$('.overview-our-service .elementor-tab-title').css('width', ($win_width/2 - 20) + 'px');
			$tab_height = $('.post-36 .elementor-tabs .elementor-tabs-wrapper').height() + 10;
			$('.post-36 .elementor-element.elementor-element-beac539 > .elementor-element-populated').css('margin-top', '-' + $tab_height + 'px');
		}	
	});

	var isFirst = true, numLi = 1;

	$(".sas-content .elementor-row ul").each(function() {  
	  var list = $(this);
	  list.attr("start", numLi);
	  numLi = numLi + list.find("li").length;
	});

	// var sas_submit_items = $('.sas-submit-content ul.gfield_checkbox li label');
	// if(sas_submit_items.length > 0){
	// 	for (var i = 0 ; i <= sas_submit_items.length - 1; i++) {
	// 		$("<input type='checkbox' class='sas_checkbox' value ='"+ i +"' />").insertBefore(sas_submit_items[i]);
	// 	}
	// }

	// $('.sas-submit-content ul.gfield_checkbox li label').click(function(){
	// 	// $box = $(this).siblings('input:checkbox');		
	// 	if($(this).siblings('input:checkbox').is(':checked')){
	// 		$(this).siblings('.sas_checkbox').removeAttr('checked');
	// 	}else{	
	// 		$(this).siblings('.sas_checkbox').attr("checked","checked");	
	// 	}
		
	// })

	$('.sas-submit-content .gform_footer input').click(function(event){
		event.preventDefault();
		if($('.sas-submit-content ul.gfield_checkbox li input:checkbox').is(':checked')){
			$('.sas-submit-content').css('display', 'none');
			$('.sas-submit-result-content').css('display', 'block');	
		}
		
	})

	$('.sas-submit-result-content a').click(function(event){
		event.preventDefault();
		$('.sas-submit-content').css('display', 'block');
		$('.sas-submit-result-content').css('display', 'none');
	})

	$('<span class="event-featured-badge-content">featured</span>').insertBefore($('.event-featured-badge img'));

	var event_slider = $('.event-featured-slider .eael-entry-wrapper .eael-grid-post-excerpt p');
	// var add_event_content = '<div class="event-content"><div class="event-left-content"></div><div class="event-right-content"></div></div>';
	
	// event_slider.prepend(add_event_content);
	// event_slider.find('.event-left-content').prepend(event_slider.find('.eael-entry-header'));
	

	$('.speaker-infoes').click(function(){
		$img_url = $(this).find("img").attr("src");
		$member_name = $(this).find("h3").text();
		$member_title = $(this).find("p").text();
		$member_description = $(this).find("span").text();
		$('.modal-content .speaker-modal-image').html('<img src="' + $img_url + '">');
		$('.modal-content h3.modal-speaker-name').text($member_name);
		$('.modal-content h5.modal-speaker-title').text($member_title);
		$member_desc_all = $member_description.split('\n');

		$member_desc_html = '';

		if ($member_desc_all.length == 2) {
			$member_desc_html = '<p>' + $member_desc_all[1] +'</p>'+'<p>'+$member_desc_all[0] + '</p>';
		}else if($member_desc_all.length == 1){
			$member_desc_html = $member_desc_all[0];
		}else{
			$member_desc_html = '<p>' + $member_desc_all[1] +'</p>'+'<p>'+$member_desc_all[0] + '</p>';
			for (var i = $member_desc_all.length - 1; i >= 2; i--) {
				$member_desc_html += '<p>' + $member_desc_all[i] + '</p>';
			}
		}
		
		$('.modal-content .modal-member-description').html($member_desc_html);
		
		$team_modal.css('display', 'block');
	})


	$('.sas-submit-content input.gform_button').click(function(event){
		event.preventDefault();		

		$('.sas-submit-content').css('display', 'none');
		$('.sms-investment-type').css('display', 'block');		
	})

	$('.sms-investment-type .sms-investment-submit').click(function(event){
		event.preventDefault();
		$('.sms-investment-type').css('display', 'none');
		$('.sms-input-box').css('display', 'block');
	})
	$('.sms-investment-type .sms-investment-try').click(function(event){		
		event.preventDefault();
		$('.sas-submit-content').css('display', 'block');
		$('.sms-investment-type').css('display', 'none');
	})

	// $('.sms-input-box input.gform_button').click(function(event){		
	// 	$('.pdf_download_link a').trigger('click');
	// })

	var download_pdf_name = [" ", "Enhanced Cash", "Short Duration: 1-3", "Short Duration: 0-3", "Intermediate Duration: 1-5", "Intermediate Duration: 0-5"];
	var maximum_maturity = 1;
	var cashflow_needs = 1;
	var realized_gains = 1;
	var download_pdf_flag = 0;
	var download_pdf_url = '';
	var download_pdf_text = '';

	$(document).on('submit', '#gform_8', function() {
		
		$flag = 0;
		$gvatiby_values = $('#gform_fields_8 li input');
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

		$email_val = $gvatiby_values[1].value;
		if($email_val.match(mailformat)){
			
		}else{
			$flag = 1;
		}

		for (var i = $gvatiby_values.length - 1; i >= 0; i--) {
			if($gvatiby_values[i].value == ''){
				$flag = 1;
			}						
		}

		console.log($flag);

		if($flag == 0){
			$('.maximum_maturity ul li input').each(function(){
				if($(this).is(":checked")){
	                maximum_maturity = $(this).attr('tabindex');
	            }
			})

			$('.cashflow_needs ul li input').each(function(){
				if($(this).is(":checked")){
	                cashflow_needs = $(this).attr('tabindex');
	            }
			})

			$('.realized_gains ul li input').each(function(){
				if($(this).is(":checked")){
	                realized_gains = $(this).attr('tabindex');
	            }
			})
			if(maximum_maturity == 1){
				download_pdf_flag = 1;
			}
			if(maximum_maturity == 2){
				if(realized_gains == 6){
					download_pdf_flag = 2;
				}else{
					download_pdf_flag = 3;
				}
			}

			if(maximum_maturity == 3){
				if(realized_gains == 6){
					download_pdf_flag = 4;
				}else{
					download_pdf_flag = 5;
				}
			}

			$('.sms-investment-title h2').text(download_pdf_name[download_pdf_flag]);
			$download_url_get_class = '.pdf-download-item-' + download_pdf_flag + ' a';
			$download_url_get_text_class = '.pdf-download-item-' + download_pdf_flag + ' a span.elementor-button-text';

			download_pdf_url = $($download_url_get_class).attr('href');		
			download_pdf_text = $($download_url_get_text_class).text();

			$('.sms-investment-text .elementor-text-editor p').text(download_pdf_text);
			$('.pdf_download_link a').attr('href', download_pdf_url);
			$('.pdf_download_link a').attr('href', download_pdf_url);	
			$('.pdf_download_link a')[0].click();
		}
		
	})

	if($(".post-83").length){        
		if($(".sms-input-box .gform_confirmation_message").length){                       
			$('.sas-submit-content').css('display', 'none');
	        $('.sms-investment-type').css('display', 'none');
			$('.sms-input-box').css('display', 'block');
			$('html, body').animate({scrollTop: $(".post-83 .sms-input-box .gform_confirmation_message").offset().top}, 1000)
	    }
	    if($(".sms-input-box .validation_error").length){                       
			$('.sas-submit-content').css('display', 'none');
	        $('.sms-investment-type').css('display', 'none');
			$('.sms-input-box').css('display', 'block');
			$('html, body').animate({scrollTop: $(".post-83 .sms-input-box .validation_error").offset().top}, 1000)
	    }	    
    }

    if($('#gform_wrapper_2').length){
    	if($("#gform_wrapper_2 .gform_confirmation_message").length){			
			$('html, body').animate({scrollTop: $("#gform_wrapper_2").offset().top}, 1000)
	    }
	    if($("#gform_wrapper_2 .validation_error").length){
			$('html, body').animate({scrollTop: $("#gform_wrapper_2").offset().top}, 1000)
	    }
	    if($(".post-36 #gform_wrapper_2 .validation_error").length){
			$('html, body').animate({scrollTop: $("#gform_wrapper_2").offset().top + 400}, 1000)
	    }	    
    }

    // setTimeout(function(){
    // 	$('.post-81 .lgip-client-slider .owl-stage-outer .owl-item.cloned').click(function(){
    // 		$client_slider_url = $(this).find('a.uc_more_btn').attr('href');
    // 		window.open($client_slider_url, '_blank');

    // 	})
    // }, 500)

	if($("#post-85").length){
        var except_text = '';
        
        $('#post-85 .event-featured-badge .uael-post__content-wrap').each(function(){        	
	        
        	except_text = $(this).find('.uael-post__excerpt p').text();

        	if(except_text.length > 250){
	        	except_text = except_text.substr(0,250) + '...';
	        }

	        $(this).find('.uael-post__excerpt p').text(except_text);
	        $(this).find('a.uael-post__read-more').appendTo($(this).find('.uael-post__excerpt p'));
        })        
        
    }

    // $('#post-81 .uc_overlay_image_carousel .owl-stage .owl-item').click(function(event){	        
    // 	event.preventDefault();
    // 	var city_url = $(this).find('a.uc_more_btn').attr('href');
    // 	window.open(city_url, '_blank');

    // })
    var scroll_top_height = 0;
    $('.team-group-info .other-team-header').click(function(){    	
    	
    	// $(this).text('–');
    	if($(this).siblings('.team-info-active').hasClass('team_hide')){
    		$(this).siblings('.team-info-active').removeClass('team_hide');
    		$(this).find('.team-member-hide-tag').text('+');
    		$('html, body').animate({
			    scrollTop: $(this).offset().top}, 1000)
    	}else{
    		$('.team-group-info .team-info-active').removeClass('team_hide');
    		$('.team-group-info .other-team-header .team-member-hide-tag').text('+');
    		$(this).siblings('.team-info-active').addClass('team_hide');
    		$(this).find('.team-member-hide-tag').text('–');

    		$('html, body').animate({
			    scrollTop: $(this).offset().top
			}, 1000)
    	}    	
    })

    $('.general-request-form #input_6_8_4').niceSelect();    
    $('.general-request-form #input_6_11').datepicker({
	    minDate:0, // disable past date
	});
})