jQuery(function($) {
    /* ============================== Triggers to the respective function ============================ */

    // Trigger to pause autoplay of the home slider
    siteSlider($('#theme-main-slider'));

    // Trigger to add fixed/sticky class to the header
    themeStickyHeader();

    // Trigger to hide and show form input field placeholder values
    placeholderHideSeek($('input:text, textarea'));

    // Trigger to fire pie charts on the report pages
    //highChartsFire();

    // Trigger to fire area charts on the report pages
    //highAreaChartsFire();

    // Trigger to fire nicescroll
    //niceScrollbar();

    // Trigger to run a date picker
    themeStartDatePicker($('#start_date_datepicker'));

    // Trigger to run a date picker
    themeEndDatePicker($('#end_date_datepicker'));
    // Trigger to run custom radio button
    customRadioButton($('input:radio:not(.lookalike_radio)'));

    // Trigger to run custom checkbox button
    customCheckboxButton($('.enable-campaign-criteria input:checkbox'));

    // Trigger to remove the add boxes
    removeAdbox($('.theme-list-remove-icon'));

    // Trigger to show/hide form ele
    customRadio($('.theme-tabbed-form-group input:radio:not(.lookalike_radio)'));

    // Trigger to open popup modal
    addLocationPopup($('.add-location-trigger'));

	// Trigger to open change miles popup modal
    changeMilesPopup($('.change-miles-trigger'));

    // Trigger to show marketting campaign
    showcheckboxOnThirdTab();

    // Trigger to execute a table sorter
    themeTableSorter();

    // Trigger to show ad section
    showcheckboxOnFourthTab();

	// Trigger to show form on CREATIVE tab clicking Email to Pay-Per-Click Campaign radio button
	showcheckboxOnCreativeTab();

    // Trigger to hide/show retargetting on Digital Rooftop tab
    showRetargettingOptions();

    // Trigger to hide/show Campaign IO based Retargeting on Digital Rooftop tab
    showIOBasedRetargetingOptions();

    // Trigger to show the hidden section on the creative tab
    showSecondStep();

    // Trigger to run image popup
    //hoverPopup($('.theme-hover-image'));

    /* ================================= Functions to be called on Theme ============================== */

    // Function to call a table sort plugin

    function themeTableSorter() {

        if($('#theme-sortable-table').length < 1) return;

        $('#theme-sortable-table').tablesorter();

    }

    // Function to show a hidden seciton on Creative Tab

    function showSecondStep() {

        if($('form').length < 1) return;
    }

	// Script for TypeAhead

	var substringMatcher = function(strs) {
  	return function findMatches(q, cb) {
    var matches, substringRegex;

    // an array that will be populated with substring matches
    matches = [];

    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');

    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
        if (substrRegex.test(str)) {
          matches.push(str);
        }
    });

    cb(matches);
  };
};

//var states = JSON.parse($('#json_for_io').val());
//
//$('#the-basics .typeahead').typeahead({
//  hint: true,
//  highlight: true,
//  minLength: 1
//},
//{
//  name: 'states',
//  //source: substringMatcher(states)
//  //  source: function (query, process) { console.log(query);
//  //      return $.get('/v2/html/get_io', { query: query }, function (data) {
//  //          var json = JSON.parse(data);
//  //          console.log(process(json.options));
//  //          return json.options;
//  //      });
//  //  },
//    source: substringMatcher(states),
//    updater: function(item) {
//        return this.$element.val().replace(/[^,]*$/,'')+item+',';
//    },
//    matcher: function (item) {
//        var tquery = extractor(this.query);
//        if(!tquery) return false;
//        return ~item.toLowerCase().indexOf(tquery.toLowerCase())
//    },
//    highlighter: function (item) {
//        var query = extractor(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
//        return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
//            return '<strong>' + match + '</strong>'
//        })
//    }
//});

    //!function(states) {
    //    function extractor(query) {
    //        var result = /([^,]+)$/.exec(query);
    //        if(result && result[1])
    //            return result[1].trim();
    //        return '';
    //    }
    //
    //    $('#the-basics .typeahead').typeahead({
    //        source: states,
    //        updater: function(item) {
    //            return this.$element.val().replace(/[^,]*$/,'')+item+',';
    //        },
    //        matcher: function (item) {
    //            var tquery = extractor(this.query);
    //            if(!tquery) return false;
    //            return ~item.toLowerCase().indexOf(tquery.toLowerCase())
    //        },
    //        highlighter: function (item) {
    //
    //            var query = extractor(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
    //            return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
    //                return '<strong>' + match + '</strong>'
    //            })
    //        }
    //    });
    //
    //}
    // Function to open a add another location popup

    function addLocationPopup(modalTrigger) {

        if($('.add-location-trigger').length < 1) return;

        modalTrigger.each(function() {

            var self = $(this),
                modalBox = $('.theme-modal'),
                modalLayer = $('.theme-modal-layer');

            self.on('click', function(e) {

                $('body').addClass('theme-lock-body');
                modalLayer.show();
                modalBox.addClass('animate-theme-modal');
                e.preventDefault();

                $('.theme-modal-closer').on('click', function() {

                    $('body').removeClass('theme-lock-body');
                    modalLayer.hide();
                    $(this).closest('.theme-modal').removeClass('animate-theme-modal');

                });

            });

        });

    }

	// Functions to open change miles popup

	function changeMilesPopup(modalTrigger) {

        if($('.change-miles-trigger').length < 1) return;

        modalTrigger.each(function() {

            var self = $(this),
                modalBox = $('.theme-modal-change-miles'),
                modalLayer = $('.theme-modal-layer');

            self.on('click', function(e) {

                $('body').addClass('theme-lock-body');
                modalLayer.show();
                modalBox.addClass('animate-theme-modal');
                e.preventDefault();

                $('.theme-modal-closer').on('click', function() {

                    $('body').removeClass('theme-lock-body');
                    modalLayer.hide();
                    $(this).closest('.theme-modal-change-miles').removeClass('animate-theme-modal');

                });

            });

        });

    }

    // Function to show a hover image popup

    function hoverPopup(thisImage) {

        if($('.theme-banner-list').length < 1) return;

        thisImage.each(function() {

            var self = $(this),
                selfImage = self.find('.theme-hidden-image-wrap');

            self.hover(function() {

                self.closest('.theme-nicescroll-holder').css('overflow-visible', 'visible');
                self.closest('li').removeClass('theme-pos-rel');

            },
                       function() {

                self.closest('.theme-nicescroll-holder').css('overflow-visible', 'hidden');
                self.closest('li').addClass('theme-pos-rel');

            });

        });

    }

    // Function to show third tab checkbox

    function showcheckboxOnThirdTab() {

        if($('form').length < 1 ) return;

        $('#theme-retargetting-section').hide();

        $('#remarketing').on('click', function() {

            $('#theme-retargetting-section').show();

        });

    }

    function showRetargettingOptions() {

        if($('form').length <1) return;

        $('#theme-retargetting-group').hide();

        $('#marketing-option').on('change', function() {

            if($(this).is(':checked')) {

                console.log('hi');
                $('#theme-retargetting-group').show();
            }

            else {
                console.log('no-hi');
                $('#theme-retargetting-group').hide();
            }
        });
    }

    function showIOBasedRetargetingOptions() {

        if($('form').length <1) return;

        $('#theme-io-based-retargeting-group').hide();

        $('#io-based-retargeting-option').on('change', function() {

            if($(this).is(':checked')) {

                console.log('hi');
                $('#theme-io-based-retargeting-group').show();
            }

            else {
                console.log('no-hi');
                $('#theme-io-based-retargeting-group').hide();
            }
        });
    }

     // Function to show third tab checkbox

    function showcheckboxOnFourthTab() {

        if($('form').length < 1 ) return;

        $('.theme-imagead-section').hide();
        $('.theme-textad-section').hide();

        $('.check_type').on("click", function(){

           //console.log($(this));

            if($(this).hasClass('display-ads-radio') || $(this).hasClass('marketing-ads-radio')) {
                if($(this).hasClass('marketing-ads-radio')){
                   $("#theme-retargetting-section").show();
                }
                else {
                    $("#theme-retargetting-section").hide();
                }
                $('.theme-imagead-section').show();
                $('.form-for-email-pays-campaign').hide();
                $('.theme-textad-section').hide();
            }


            else if($(this).hasClass('email-pays-campaign-radio')){
                $('.theme-imagead-section').hide();
                $('.theme-textad-section').hide();
                $('.form-for-email-pays-campaign').show();

            }
            else {

                $('.theme-imagead-section').hide();
                $('.form-for-email-pays-campaign').hide();
		$('.theme-textad-section').show();
            }
        });

    }

	// Function to show form on CREATIVE tab clicking Email to Pay-Per-Click Campaign radio button

    function showcheckboxOnCreativeTab() {

        if($('form').length < 1 ) return;

        $('.theme-textad-section').hide();

        $('.theme-report-tabbed-form-wrap input:radio').on('click', function() {

            if($(this).hasClass('email-pays-campaign-radio')) {
                $('.form-for-email-pays-campaign').show();
                $('.theme-textad-section').hide();
            }
            else {
                $('.theme-imagead-section').hide();
                $('.theme-textad-section').show();
            }
        });
    }

    // Function to remove the ad boxes

    function removeAdbox(thisAdbox) {

        if($('div').length < 1) return;

        thisAdbox.each(function() {

            var self = $(this),
                adBox = self.closest('li, div');

            self.on('click', function(e) {

                var confirmBox = confirm("Are you sure you want to make this creative inactive?");

                if(confirmBox == true) {

                    adBox.remove();

                }

                e.preventDefault();

            });

        });

    }

    // Function to check custom radio button

    function customRadioButton(customBtn) {

        if($('form').length < 1) return;

        customBtn.each(function() {
            var thisBtn = $(this);

            thisBtn.on('click', function() {

                thisBtn.parent().parent().find('input:radio').removeAttr('checked');
                thisBtn.prop('checked', true);

            });

        });

    }

    // Function to check custom radio button to show hide country/state/postal

    function customRadio(customBtn) {

        if($('form').length < 1) return;

        customBtn.each(function() {
            $('#geo-postal').hide();
            $('#geo-state').hide();

            var thisBtn = $(this);

            thisBtn.on('click', function() {
                thisBtn.parent().parent().find('input:radio').removeAttr('checked');
                thisBtn.prop('checked', true);

                if(thisBtn.hasClass('geo-country-radio')) {
                    $('#geo-country, #geo-country select').show();
                    $('#geo-postal, #geo-state').hide();

                }

                else if(thisBtn.hasClass('geo-state-radio')) {

                    $('#geo-postal').hide();
                    //$('#geo_country_select').prop('selectedIndex',0);
                    $('#geo-state, #geo-country').show();

                }

                else if(thisBtn.hasClass('geo-postal-radio')) {

                    $('#geo-country, #geo-state').hide();
                    $('#geo-postal').show();

                }

            });

        });

    }

    // Function to check custom checkbox button

    function customCheckboxButton(customBtn) {

        if($('form').length < 1) return;

        customBtn.each(function() {

            var thisBtn = $(this);
            if(user.is_billing_type=='PERCENTAGE') {
                thisBtn.closest('.theme-form-group').siblings().hide();
            }

            thisBtn.on('change', function() {
                var type = $('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked').val();
                if(thisBtn.is(':checked')) {
                    //console.log(type);
                    console.log('hi');
                    thisBtn.closest('.theme-form-group').siblings().show();

                    if (type === 'TEXTAD'){

                        $("#max_impressions").parent().hide();//("display", "none !important");
                        return false;
                    }
                    else {
                        $("#max_impressions").parent().show();//("display", "none !important");
                        return false;
                    }

                } else {
                    console.log('no-hi');
                    thisBtn.closest('.theme-form-group').siblings().hide();
                }

            });

        });

    }

    // Funtion to pause autoplay of the home slider

	function siteSlider(sliderElm) {
		if($('#theme-standalone-slider-section').length < 1) return;
		sliderElm.each(function() {
			var meSliderElm = $(this);
			$(meSliderElm).carousel({
                pause: true,
                interval: false
            });
		});
	}

    // Function to add sticky class to the header of the theme

    function themeStickyHeader() {

        if($('#theme-masthead').length < 1) return;

        var $themeHeader = $('#theme-masthead'),
            themeHeaderHgt = $themeHeader.outerHeight(true),
            themeHeaderTop = $themeHeader.offset().top,
            staticHeight = themeHeaderHgt;

        $(window).scroll(function() {

            var thisWin = $(this),
                winTop = $(this).scrollTop();

            if(winTop > 20) {

                $themeHeader.addClass('theme-sticky-header theme-fixed-header');

            }

            else if(winTop < themeHeaderHgt) {

                $themeHeader.removeClass('theme-sticky-header theme-fixed-header');

            }

        });

    }

	// Function to hide and show form input field placeholder values

	function placeholderHideSeek(input) {

		if($('form').length < 1) return;

		input.each( function(){
			var meInput = $(this);
			$(meInput).data('holder',$(meInput).attr('placeholder'));
			$(meInput).focusin(function(){
				$(meInput).attr('placeholder','');
			});
			$(meInput).focusout(function(){
				$(meInput).attr('placeholder',$(meInput).data('holder'));
			});
		});
	}

    // Function to run a date picker on Campaigne Schedule page

    function themeStartDatePicker(pickDate) {
        if(!pickDate.length){
            return;
        }
        if($('form').length < 1) return;
        var allow_times = [
            '00:00', '00:15', '00:30', '00:45',
            '01:00', '01:15', '01:30', '01:45',
            '02:00', '02:15', '02:30', '02:45',
            '03:00', '03:15', '03:30', '03:45',
            '04:00', '04:15', '04:30', '04:45',
            '05:00', '05:15', '05:30', '05:45',
            '06:00', '06:15', '06:30', '06:45',
            '07:00', '07:15', '07:30', '07:45',
            '08:00', '08:15', '08:30', '08:45',
            '09:00', '09:15', '09:30', '09:45',
            '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '11:45',
            '12:00', '12:15', '12:30', '12:45',
            '13:00', '13:15', '13:30', '13:45',
            '14:00', '14:15', '14:30', '14:45',
            '15:00', '15:15', '15:30', '15:45',
            '16:00', '16:15', '16:30', '16:45',
            '17:00', '17:15', '17:30', '17:45',
            '18:00', '18:15', '18:30', '18:45',
            '19:00', '19:15', '19:30', '19:45',
            '20:00', '20:15', '20:30', '20:45',
            '21:00', '21:15', '21:30', '21:45',
            '22:00', '22:15', '22:30', '22:45',
            '23:00', '23:15', '23:30', '23:45',
        ];
        var now = new Date(); //console.log(now);
        pickDate.datetimepicker({
            format: "Y-m-d H:i",
            minDate: '-1970/01/1',
            minTime:now.dateFormat('H:i'),
            allowTimes:allow_times,
            closeOnDateSelect:true,
            onChangeDateTime: function(currentDateTime){
                if(new Date(currentDateTime.dateFormat('Y-m-d')) > new Date(now.dateFormat('Y-m-d'))) {

                    //console.log('true');
                    this.setOptions({
                        minTime:'00:00'
                    });
                    // console.log(this)
                    //this.datetimepicker('destroy');
                    //$('#start_date_datepicker').datetimepicker('hide');
                    //console.log('true finish');
                } else {
                    //console.log('false');
                    this.setOptions({
                        minTime:now.dateFormat('H:i')
                    });

                    // $('#start_date_datepicker').datetimepicker('toogle');

                }

                //var current = currentDateTime;
                var start_date = currentDateTime;
                if(user.is_billing_type=='FLAT') {
                    start_date.setDate(start_date.getDate() + 14);
                } else {
                    start_date.setDate(start_date.getDate() + 7);
                }


                var tmpDate = Date.parseDate(new Date(start_date).dateFormat( 'Y/m/d' ), 'Y/m/d');
                var minDate = new Date((new Date).getTime()-tmpDate.getTime()).dateFormat( '-Y/m/d' );

                $("#end_date_datepicker").datetimepicker({
                    format: "Y-m-d H:i",
                    minDate : minDate,
                    // minTime:now,
                    allowTimes:allow_times,
                });
                if(new Date($("#end_date_datepicker").val()) < start_date){
                    $("#end_date_datepicker").val(new Date(start_date).dateFormat( 'Y-m-d H:i' ));
                }

            }
        }).val(new Date().dateFormat( 'Y-m-d H:i' ));

    }

    function themeEndDatePicker(pickDate) {
        if(!pickDate.length){
            return;
        }
        if($('form').length < 1) return;
        var allow_times = [
            '00:00', '00:15', '00:30', '00:45',
            '01:00', '01:15', '01:30', '01:45',
            '02:00', '02:15', '02:30', '02:45',
            '03:00', '03:15', '03:30', '03:45',
            '04:00', '04:15', '04:30', '04:45',
            '05:00', '05:15', '05:30', '05:45',
            '06:00', '06:15', '06:30', '06:45',
            '07:00', '07:15', '07:30', '07:45',
            '08:00', '08:15', '08:30', '08:45',
            '09:00', '09:15', '09:30', '09:45',
            '10:00', '10:15', '10:30', '10:45',
            '11:00', '11:15', '11:30', '11:45',
            '12:00', '12:15', '12:30', '12:45',
            '13:00', '13:15', '13:30', '13:45',
            '14:00', '14:15', '14:30', '14:45',
            '15:00', '15:15', '15:30', '15:45',
            '16:00', '16:15', '16:30', '16:45',
            '17:00', '17:15', '17:30', '17:45',
            '18:00', '18:15', '18:30', '18:45',
            '19:00', '19:15', '19:30', '19:45',
            '20:00', '20:15', '20:30', '20:45',
            '21:00', '21:15', '21:30', '21:45',
            '22:00', '22:15', '22:30', '22:45',
            '23:00', '23:15', '23:30', '23:45',
        ];
        var now = new Date().dateFormat('H:i');
        var start_date = new Date();
        if(user.is_billing_type=='FLAT') {
            start_date.setDate(start_date.getDate() + 14);
        } else {
            start_date.setDate(start_date.getDate() + 7);
        }

        pickDate.datetimepicker({
            format: "Y-m-d H:i",
            minDate: '-1969/12/25',
            //minTime:now,
            allowTimes:allow_times,
            closeOnDateSelect:true,
        }).val(start_date.dateFormat( 'Y-m-d H:i' ));
    }


});