$(document).ready(function(){

    var so = js_data.so,
        start_date = js_data.js_start_data,
        end_date = js_data.js_date_now;


    var impr_array = [
        {data_filed: 'Impressions spent', click_count: pie_data.impressions.total_impressions},
        {data_filed: 'Impressions Left', click_count: pie_data.impressions.impressions_diff},
    ];

    makeChartPie('total_impressions', impr_array, pie_data.impressions.impressions_percent+'%');

    var click_array = [
        {data_filed: 'CTR spent', click_count: pie_data.clicks.clicks_percent},
        {data_filed: 'CTR left', click_count: pie_data.clicks.clicks_diff},
    ];

    makeChartPie('total_clicks', click_array, pie_data.clicks.clicks_percent+'%');
    make_all_charts(start_date, end_date, so);
     
    highChartsFire();

   function get_data_ajax(value) {

      $.ajax({
        url: "/v2/campaign/combine_report",
        type: "POST",
        dataType: "json",
        data: {
            page:value,
            start_date: start_date,
            end_date: end_date,
            so : so,
        },

        success: function(msg)  {
        
        $("#table_all tbody").html("")
            if(msg.so_compaign.length != 0 ){
                if(msg.so_compaign.length > 1 ){
                    for (var i = 0; i < msg.so_compaign.length; i++) {
                        tr=$("<tr class='so_number'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_number').eq(i).append(td);
                        }     
                       a = $('<a href=reporting/'+(msg.so_compaign[i].id)+'></a>');
                       $('.so_number>td:first-child').eq(i).append(msg.so_compaign[i].io);
                       a.append(msg.so_compaign[i].name);
                       $('.so_number td:nth-child(2)').eq(i).append(a);
                       $('.so_number td:nth-child(3)').eq(i).append(msg.so_compaign[i].campaign_type);
                       $('.so_number td:last-child').eq(i).append(msg.so_compaign[i].campaign_status);
                    }
                }else{
                        tr=$("<tr class='so_number'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_number').append(td);
                        }     
                       a = $('<a href=reporting/'+(msg.so_compaign[0].id)+'></a>');
                       $('.so_number>td:first-child').append(msg.so_compaign[0].io);
                       a.append(msg.so_compaign[0].name);
                       $('.so_number td:nth-child(2)').append(a);
                       $('.so_number td:nth-child(3)').append(msg.so_compaign[0].campaign_type);
                       $('.so_number td:last-child').append(msg.so_compaign[0].campaign_status);
                }
            }
            if(msg.email_reporting && msg.email_reporting.length != 0 ){
                if(msg.email_reporting.length > 1 ){
                    for (var i = 0; i < msg.email_reporting.length; i++) {
                        tr=$("<tr class='so_email'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_email').eq(i).append(td);
                        }     
                       a = $('<a href=email_reporting/'+(msg.email_reporting[i].id)+'></a>');
                       $('.so_email>td:first-child').eq(i).append(msg.email_reporting[i].io);
                       a.append(msg.email_reporting[i].name);
                       $('.so_email td:nth-child(2)').eq(i).append(a);
                       $('.so_email td:nth-child(3)').eq(i).append(msg.email_reporting[i].campaign_type);
                       $('.so_email td:last-child').eq(i).append(msg.email_reporting[i].campaign_status);
                    }
                }else{
                        tr=$("<tr class='so_email'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_email').append(td);
                        }     
                       a = $('<a href=email_reporting/'+(msg.email_reporting[0].id)+'></a>');
                       $('.so_email>td:first-child').append(msg.email_reporting[0].io);
                       a.append(msg.email_reporting[0].name);
                       $('.so_email td:nth-child(2)').append(a);
                       $('.so_email td:nth-child(3)').append(msg.email_reporting[0].campaign_type);
                       $('.so_email td:last-child').append(msg.email_reporting[0].campaign_status);
                }
             }
             $(".theme-pagination-wrap").html(msg.link);
             click_pagination();
           }
        })
   }

$('.pdf_export').on('click', function() { 

  // Define IDs of the charts we want to include in the report
  var ids = ["users_gender", "placement_chart", "age_chart", "total_impressions" ,"total_clicks","theme-area-chart-holder"];

  var inp;

  if($("#status").val() == "" ){
    inp = "No Sales Order";
  }else{
    inp = $("#status").val();
  }

  // Collect actual chart objects out of the AmCharts.charts array
  var charts = {}
  var charts_remaining = ids.length;
  for (var i = 0; i < ids.length; i++) {
    for (var x = 0; x < AmCharts.charts.length; x++) {
      if (AmCharts.charts[x].div.id == ids[i])
        charts[ids[i]] = AmCharts.charts[x];
    }
  }

  // Trigger export of each chart
  for (var x in charts) {
    if (charts.hasOwnProperty(x)) {
      var chart = charts[x];
      chart["export"].capture({}, function() {
        this.toPNG({}, function(data) {

          // Save chart data into chart object itself
          this.setup.chart.exportedImage = data;

          // Reduce the remaining counter
          charts_remaining--;

          // Check if we got all of the charts
          if (charts_remaining == 0) {
            // Yup, we got all of them
            // Let's proceed to putting PDF together
            generatePDF();
          }

        });
      });
    };
    };

function generatePDF() {

	var layout = {
	  "content": [],
	  "styles": {
		  "fillColor": "green",
		  "color": "white",
	  },
      "footer": {
      	"text": "Copyright (c) 2017, ProData Media. All Rights Reserved.", 
			"alignment": "center",
			"fontSize": 8,
      },
      "header": {
      	"columns": [{
              "width": "30%",
              "image": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOwAAABICAYAAAD1XhnsAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkVBNDcyN0I4MzNCMjExRTY4RDhBQzkwQTgxRDJFQThGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkVBNDcyN0I5MzNCMjExRTY4RDhBQzkwQTgxRDJFQThGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RUE0NzI3QjYzM0IyMTFFNjhEOEFDOTBBODFEMkVBOEYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RUE0NzI3QjczM0IyMTFFNjhEOEFDOTBBODFEMkVBOEYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7CldroAAAnWUlEQVR42uxdCZwdRZn/qrvfMWcmBwMkRkEgsMghiMgirosED6KgyLHLsYiCi6Coq64riCCyiNcKi6KiuBg8QFZlZWFVBLnvILeAhoQghiSTZDLHm3d119bX/a/X1fX6vTkyMSP2l19l3uvXR3VV/b+rvvpK0Js+T+OSVMUPiAIZfee//FHie11Gn9Up5OCaeoBzcJ6g6Hc+wOdIEZ2TU1/KRL3zhunI02+lWsVr8XihbiFJqr9BeDv1mfizEOpzr+PIN6gPx/i+OPvaD9z/PGWU0UuQnL/UiksZsoAF+Vxwft+s2ovzt6tcP6unfqLCcj7r1oxequT9ZYJV5HNecEJPd/3Snq56Zy7vk6NYT6XslJQcrmXdmlEG2BmBVNbGRXdH0f/qnL7qKZ2d9fCYH6raoZJcVmLXz7o1owywM4AULns7O+o/3GZuZUmhEFDdh0nsiFDCKmu2TkIGWbdmlAF2K0tWVTo6OvzvbDu3ukTZraFUZSNWhP9JfWJNwLWVUUYvRZpRTifJnuMW9Szk/Y9sM6d6dF7ZqwHA6oRgjUArROg6LqvPmUqcUQbYLSvnAxpe00u3/GRf6uoZI9cNGkWIQKm7cv/ZfbULle2qwCogWg2wxl9roSmbUUYZYLewdA3nVwXlCnVb0nZ3ddaX9nTVQmWX8cjStFEomuMV4ZvIqvqeqcQZZYDd4qSk6dj6blr9h23JzdVj4evRKX29tV2UlA1FpyVRY3WYwi8VjqXIujWjDLCTFpkURTglCo6nkbJNNzyzLf32V6+iYldV32Jed1ftCx3FIJS+WpKahRxqSFr1p0ZSZIDN6CVL3rQCNEQVFVSZRa7YRiGqXyGpS/2mniPr5NOoMkIHFHjXkh9sUueXGxDm/3O+Amsl/MIAzHnytJ6eej6AJI0flOQ4IvwxlL+ZlzijDLDjUiDnkSd2p67c66noHkqe8zLKOXPJdXoV8rxQNIaxxgq0tWCEqv56qtZX0Zh/M5Xrd6rjT6q7DJAj5ejGTgpqObZl53XkxfsVaEMsRlI0SQ2YCgnhLatKzE6708k74HPZSPkrpvq95/w5x08OuGTJFdjP3xzAspdoJyp6R1Ff8SQF1ldSh5dXkjU2NE3xGyGKJW2fkrR94bV1/2Aa9as0XHmeRirfp1F5da3qPS19IQvF4LDOgr8wQiTA6iR1ePwU/R7+paqYgg2LBnVS+EFCwW+j0M8Ec8bmZ8EMq+9MMevkDG+XN6tyhSq/V+W3qtypxud1CrTVqQE2etVtqeCeTb0dx1JPoT9ccSMMkUeUYrNiNY/AZ/6bc4l63Tz15BR4O85Vxz4q3A0HB778bT5X3yeXN4Cv/joaoSK+hRQilrSS6o4zpc54uSpnsesLA0D/9XDr9ao8rsr9qjyjSmkrd2q3KueE/cBTWXF9c/jMpgavWHoInf4Cznup0e6qfEyV0QmCkBeG/EaVH8/gd3LRr1wOUuVDqrwMfThJwLKEdMU/KJB+VUnVeSFQGxLUYvANoxPtKIG4gJLL7iTOKaqq9Lm9InC2CX1LgnLhlA0vy9G4dQwxIjRIZfQsGarF1Slyz/mq/PMEz71XlX9lzrcVOTUD9kxVihM8/79U+boqy/6MdWRwzFZlwxZkFjur8t5JXtM1wwFra4g8ZeJPXp2QqvGL3lLq77qK5nWwzYr1sZEuakrC5GeR1NqajgHMvtTrbSu4R+rC2Hgqx9QBpX5oTYhgKiAqT0JqHqDK7aq8Yyt2Knfgmkmcf7IqD6pyEm35qTxW6T6jyvdV+bUqC7bgs0a3mt9mqzmdZKp1mlSB2d7s8q6l2R37UMEBsCzw6UXpwsCPgEQNzzVUYmm5jYQxjISsKukqhBOqd8k6iRRLLXlObYpST6QM5JtUuRhq5mmqvNX6/Ueq7KrKH2dIX65BfQdV2UGVY1TZ0TrnSjCma7dQHVji/9JihFtSC0kbDQ+DOeVa2Lu/meGYFO0B66UwXK2qRsJqV+rK30SzCwtDh5IvjegF6KWuAVqBiRUBNVkYKrEJWpspaB2XRN3hcEQK8lKp841IQ611S/uVRJz5QioJ60zbAPk/VX4BFYU7+aeqHGL83qnK8ap8YYZ09D2qfNEAyJcg6c5MAe1dqvxpC6nqJq2bohTcHLpQlZ+0GfgzfdpvdBzAinSFK8rDsgN15m6iWfmFUYoXSalzK7b66xgAlRYwRVueGwglYRXoeGoVbCC6gC/RJrCB4cYZQTT1408jR2cbTLJL3Tvgc0Pq8+UWYJleZb1RLxwhywy7rUOVfVTph3R7EHadyfXZ6bWHKtvh+1o4t34/SfvPdPOxo+yTsM+PshjNJ1T5lzZtxXPpO6myvSo9qPdG3HNAlWGr+ftxzeIUe/F1cHyxKjpivbtJ3E67oA06cO4gzl+LOkykbwenCEp+5iK8N7/PmCrPscRW/T/YamoHMwyz0O+s2fShrZ5V5VFoGe1oFp67DdryHZNXiSOao2zWG6hbgZWACEeMp5I2D59xEJo4WbLTSHAAP/8rSOMeLGmjuH+RUASEIWQDKeqeO20M1FY9nkk5R8dQcif/myp/h8ZnW3EpPl+jyl7G/d4AhxWhky6C+tqdYlffDi/hM1NUpcpQk4+03udYVc5Osdv5Hm9S5XwwkF7jN3boDQE8q+B4ewwM4LuqHEjN6Xn60A6jAP5leK7tFT1ClU/DxOi06j8EJrEcpsl4mkFuCv38FlU+CwbcmdLvZypg/tIGrTrG7/s+VT4K88OzJDn386kt+q8IR+dHwRgnlNrIa/nSnnOpAuvuDTXWEZZ/pwV/b/osJycfBPlO5FQqmGowz8Q6Sh23ndGR9q3gLB0Gc9URW0xVKVAyzZwp0eaqcopx/HwMsO8CtJpYajyBz+yqvxVgb2UPsgPnDoD8mSnWm6ejHoGU18T1fSV+M4HDoDmvjcYxD2V32PCPYTRsD3CmgWEOCtOLKW16MYDYqg2KkHj8zM9PALDlSbSNB6Ce1eYc7r/roCk8aoCVGez3wAxbMQJm4Ner8nowHbP9WW1/4+ZKERwVx1Cnd9yEhWNj6Jq6qmy+dvz7BaFCHonRnGz6UcTTu40Q5Xj6SH0tT6OHo2ypfW9Laa8X8PchOKk0vQIe0kXW+TdAYrgY8DulcPMB6xgP1m/S1JPLDUMltcG3h9Uzp6aAldXRuyFVbaoYPV+fYF0esZ55bgpY2Xn2QAvVeSLmwd4Uz2OaZX6KgDoxBaw3GL4Lk3Gcq0AqAFbuvy9bYGUN5Ieouw34JRaTWpoC1hVgoCPtAcuWoi6ReHo55d2vRM4omYSSHfUnZTNQAyPQX+urppNKUJtYk3BpnI94fjexZkDGkpZBinB/Ip1hNVrUXhXTZ8MymOapztkGqsuHU9Thqw2r/+cp0sG0q/j8G3HuwRRNipvEqtHfqrIvRXO9JvH5u03xPYIWUmkH4/OOcFKZ9AsM/rfCuTbU4v4l2Miszl5q/cbXHIe2XATHmKZ9YUub9B08czFUzfIU3vd8gGaZVZ4A89O0HcwRky5Q5WiUH1q/vRJgI6j/p1rM/T0UTZ8dbmkuTHta5shh1u/cDvtDkzpvMiqxIE+cocC67fj2pmHPSuOY9hSH2dGkBWBcoL3KviGFI0AGHFoookmWXMQkdAbipLiTdnUkQDR9KjEP0v0omvxflPI723C/M2p/b4v7XIZOkJB2IoWr/x7q1UZIFvbs3m+d8xaooFNhSJtSjs01mvM4y4bmOnAE0R8NyVi2bFpTt1qFz3fC5jal+00pWgOPktOt8bccKrmeX34YUqs4yXftTvEHaAY7aLVnv2WuXAJnE9PKlPf0IWWPs7QtZkQ/VjYu/74Ozrk0YzEPxmwzxksNjeWh9oAVhm3qKC7pidNChU2mNLEpGYVhYIbTOwArI2dMtc1obS1V6g8qUD6myqAaFn3qvD0p57xGqdvbUiEXAVd7pDXq2NPrsHtJeryonV3DbKMGeBTP2gSGGR1K2yixE4vl2jTasH2wW2zijv0gRYEBZiutxaAwB+FtAPaoJXlfZd3zPgzudg4udgb9B01fVkjfcNLY3t2nrDoUaWIBF8UUgVBs0bb7pajLay2v7XQGedxrgCIH/4BJTxiMZU9IS5Puh0rOzrPXWL/9HGDlz29X5bUp/cu0M4pJNxv10p71doBt8Fn+d7xSi3uTjiUZu2GFnvMUSdAGyOZfUmNgqHo3laqfp3pwt0IWL6erhUMjis7PqUs7aKN4DXV4n6K+/CHUkTN2BRBhGJaIsO/6MjremM4VegVfEikitmnr02jD1iFV+O86NPqv4Cj6YwpLq6KYgP0hNc+rdacMxI2WzeRDys2xVFhnioCdnXJsHf72WuoxQYWuT+E5E2WXc+B0s/0B09F9HIL5A6sfcrARdduxJ/hvUqTgXiisES2wmPF5mOLrh/pv0s7q+N5gqp+xPM03wxRiWpgi/VdP5uU8Y+hsowB5fCIG2HTJ2kEL0nJbbapUaajyYarJpRT4pfA+LiIkGpFPohJyk6p/Mw0Hd9FY9d00q3g59RU7Q6keyEBJzCDCp/C0JHXMoAmZohI7jV1E/GmUsP+Fxi+hoyvjDGJJzfN/Ay2kkNPGv65pLGX6Yypv52CgUIqTQzOQnnE8rYKmN6SvK8WrPDJF8Nt0nWUrtzIFbfV+Pzjn7L7hudh3K7C+aLSXfe0HVDkj5TnssDuB4umzWRPs5zadyQl9o6S+ypAWOyZX2RgGZkBWBJT29kiWqkM0WFlMteCbqplLTbHCTXw4tH/LSh7+gDaUD6L15cHIP+wF/fMHZf+CQfL1uhtWh3klXyDCvE8N/5fhOQ4Ts0U29XTasGyfrlEdNQQpWZ8iWNIkN6VMb9g1z6U4cKYyyZym2lcNj22QUid70PSmSIbNaem0Z+ZSpHB+QrMa7e8zUebqWfd/AF7sfdUYWGbVvV0/Mzg5Mo4XJSw2gN6q721GuH27373GA4U4KAKkoX+aDiHHUo31jGSpXlb26jvVOXdMugsjL/Jvaai8WDXz7dw/Ti4IXCVbJRlL0WUcBWkmaDNDkqNWlNOZ4rQ4DfdIa5E08C/EQPONAWBLoAenCNg3pjjNnjccSjqiqN8CqKnI7JMysHITBGZanYegkptq56wUmz2fIpmnQy2vUfO0EffJlXB2PQyGNtLCh2GbK2tw7dNwGj2lgFpJuVZHYTmWiWTW/YgWDF2rxCLqID84vAkJ2jg0AWoiqBqwc+ksJWVvRUbvqVkhUiyjwfKZNFq82HGldBzpyIDzT4jG4vW2eSRko0qB48z4RdsleG1NgByI76sMLmsP1hvHAWxaWCZPp1yQcu7FhqNjBB5RE9SvgB02isFyYso9tks55qYMNKeF1/pPFmAXgSnUoaIfn3Ld/GnqgzE4mV5nAfYrcLglRVzkGZaIdBrAOQda/oALoY2Z15HF+F4EszKZsWlLvzLFGRaZq5FqzpI1PMANt0vCCyxlMlpJUHJROn+u1JcpsF6uQC/DmGRekD7V9fx1+SOqVh8qjRaLpZGidISM51xTcriZy2nD2SE/WsAuaMaTD6eIrbaei47pB6BsiTSeXcZ9uCPuwU6R0wDy3a3zHoV9bkob+94MnvdA6r2fmmOomfY3OX8LW7wP9t1OqMd+GMBDKcBgz+s74CA7N8WbSgDYdHiO69S8aoc1qn+nKK47D2m+I5jVl+C11gxuWUp7naUAOleVgiq9qvBMAMdyf9zQTlZQ8+qu4+FV5qnUb7R4v711mhkHK2d2baCCLFBKy37Vtivv7VoLvqTuMJpY+saL2nNOvIdsW83FdGqJErny0sENPc7ghm6h1GJfK1Sh/RrqvZEdG9uzxt/Y0v5LoMtSHDts8/A8Joci2kv5zm3hwDJpH3gk+R63o/MXpXgkj7Y819x0V1vqHw/YL8Ij/p8YRI9aHs03YJDZdr9t252Fd+Ipru+BCXA/fdN6ZjfUSq77x3Dt85Sc6jnGAM7m0o3UHAF2JBxFN6Etud5LUZ9wKkdJUYn3GLLa65Noez2TwOUieJznG5L96yk2K4cv8gqqQ8FMVlht848a9E648pTD1OxMEIaqGS+PMSBRC7gxbwkdVp5RCkrKFpV505WPgNsUXwh1OkxRqv+SDri4yXWCdZ4XuAKzRfpxMjBiMIzopvBz0IjLqImpiVg3RQWdbBC5SLlHK4/fOgy+IIVT2yD7FsAnx7Gx85AIiyx1W9uSv4AalzbH+zQkgUmsEr8an9lmezclo38YrKdbEoEB8HjK/XlQ6hhkrUreA2ZgO7d0yCQn5uPghp9ZzzxqnPZ1J9hf/E4fpuYILtZU/g7S3FTZ32skU2M79d9S+m83XLsP3le3o6l6XwlPNlnvpcNU/5eiUEYzxPEQaCcRTNRQeIUVcdQMWt9QlcMIJmUDOM5AqokfDl0vAqQ5zHw53uKBTUpijilJ6sQhiSI1tbG0qxpp7VNViQUcBwMoG4gS8RkT9QgPGfcYGGfwXI/B+xuKQxc1uEoAFkvdMyk5sa6fVUI9zTJI8ZK0NbjHV+B4ehc1R++YUvbbUEmfxHuUweU5Cov9G3+AKr0WkpbPv8G6TwVS4rs4Zwj3GEJ9Hqc42khC+vwT7LMheK+1ynkMJPZV+G0F3uVpSk4ubrDafDIG2R1QR6/Be1UNR1kZx7gunyAjjBFS9ltwjN2OOpjXlvC+3Lfvg7TWVIaafQ7aqIRrBwHk0/DeV6ItWLP5lHaSeeHKUyHnRmJKxEvopOFwCoe0FY4YccgoYkK2WFNbyEdV4cgn7eZ12mitdZfm9A+FZcO63kCvfQ9EvDI2kSZcxAI75AOOaqypZTl9xHIiuOj8yajYa8AFXQPsa8e5hoMx3mZIxj503krYeINtJPQbDYluGiU6uGIEzp3yBN8jAHe/Fc4PtoWHARAd2siOmoPx/PUt7suOlVMhYbaHJlDC+evxfqb9fBWY1w64ZgOeOWqMs4PgpNpgAfIO2NK+1Q+ToWdgR0Z+nKgOEu/4LO5XAUjJAC2/+21K6r4ZTrqdoSFUUdeVBpDTPM3/DtDvBHPgT2CK+nzOO/UYjg3rpX0eurczjvWVhkdYxDZtY0pHDxG5ggI33Faju3O0xc5zSjqqV3CGyyTqPtXrDo0M9BrbQ9rKjKTu3hLVqp4MfKemR4MGqbSd0BoWQUPaTmWulJBCcuU0OJOem8J1FYDzqUlcE8C+2xKkuXorenKC9Vs7AYZlTnc83OZej7Xx9i6fJkfgKsNLb4+PdmOnAtA/wypz2rkt8hJrprCuxa3L2sa28hKLKFu/NKRoAwwyGSMsDMT4btlT3w994x30ur0fo7FKIV3XUud3dvpUKPi0abCTfnD5oepSvzm2ourRgp1W0+Ij7qOhwS6pruOoJ1RBpvsgiRKr+ES2b11GW5E2N+H4RMiDIpVvGTQRLmAXyTlZJfNygVs96MC76aDXLqOB9XOUOZuudbFjqF6PzK7Orgp96FM/o3LZjZbDWaD1fYfGRjvIc2W+GlCfTCRrm5AlOqV0mooDzoJhH1C21UdGE5OQf87nh2mDFEO420P2lUIjWEsDUzuMtJfYMUDru9TTO1Q98LUP0vBItyNE8xaPOs+S58nQkUxwIJXHCgrA6m/ZaRxPXBeJyb5AipeT5hWoV1MAhcVHVBmaYptU4dTIwJrRTKUR2LCh9CqE6HJ1VkMjE6GZNKmRRC2c/VS6u+DplznCoQFhbqQMj66XY79TEG8HCXC56jkMZD8Qid8MWhT4optX5jRSsKVN6yaPldS91k1RlRmj5jm5GcNhM9r6qm7aGJiEvTptqraWsB1NhmdTHlJhfmUUlgMZiuGDlDr8SyHihF58JscDu66lSTekKIU2LQOSHVGWaHarVXGYAiyZSdjGI0fIF8TEnRwzzjbJ6KVhn27pseJgr8bORKxfImkS2TlaGianIxiy8u0K87vZmyy7TpTEJW2WRQOYN2kWzZkWX1GtOSfW/VhSp87BWrmd1MOWqzKcDa2MXsrEoYlOw4aNk4cbIUYp4YoBg1HUFeBk3XdydV+c7bnSyympmstJVfyJOYqUhM15PvF1rCIrVdn1A/qXWtXpjcIOm/fTagoojm3fR8JIJyfr1Ixewg6wCLCy2MgaoWMChQFa0ZQUPFyGyrZofYxKQ8O5E/NecLiXkz/dnMoogB5WKnln1Gsi3F093JkuaMJmM9dRklop5/dOZatJk2644YZsRGTUkpYsWTLuuEk7ZzrGlb6vRxE0io1pHdteFWk2baQS42BuZNQlJV6vmTertli4YZD3pAg+rgNHRr3/HhtzY0eUbN6Gx65G6NwSctBxNm9nNtWovKTrAG2KmO9H0aoUdkrZ2Qc5ioeD4DkaJi1BGrNADn3j2F6OZuFpJ56c59Cz4VSNJ4p44thTDhjX21zys3VmAvY36Igqz7iO63wb2Cwv2VqIY3XUy8O78HPvx3Gu1564xjXutY5iJxxfw4Hv2+LZ7FHndC4cjTQ4XtdSFF/LUVnz8O4cI/sUNWf54fpy1kiOsmJfxD14hm+dx+3D6WXuoPQoog60H/eVGTc9F+/xCPqL++4gvG/N6m8XfbQKnzk2uKDGyD0KOIExZgTqwnmHd+B2U8c4eu136jy73twnHD12FyUDfDhKag/0nRnwz+3NoY8cfbVe3ZfHwT0MWDe59SMZ0tRM0GYukm0MagZbGB63aSivtGLxq1l9tZOUevsTKUVtYmCVOd8X7xwa9r4/NMI7wsrQrObICXNaWBhRTWYzwMH134rtrE6Ptpow8cD9VZvfOdD9G9YxjhP9GkWhd5wLyI66YSagsykOYZBwe3EkEa/AeDKh7UQrWz6L73yvBcbAugvHOYzx9pT6VTEgOKSPXZWtElxvwCDgv++kKDzOpgfAvLi1twc4PLxnHp+5fpwh8RdtFCC+/08xVp4EeDm8cX9j0HKn/QNFsbN87xUUb+LFicPPpTgfMYPnQrwbAzxtIzIGPMfwXoM21nU7BMc44fsVaNub2vQ3x+9eRFHwPqewnY1SNsDK9f4OznkO4GP6gvr9Mwq0VaPefC+O1ea45K8Y4uef8azXUZwpk8F/Hxjq42A23A8Los2Lw3ABEad8CYwldYG1cC36PUzoIqJApIKOaNw4nMuvW5//UbnsXaGA+DdK6nltpKrrCLmoXPEu27Ah/+OhITevQx8D6NxcJ/Ykh0vqKPoc/qU4bUw4ReTStZyAbTM312aJwqtTXkVxqs7rMbhejc42ieNGP4pBzR32/jaG++PgwjtiAO6F++WMQXsSwMpMY188l1e3HEbJKafAqBtz5p0AhD3AoZmzfwR1PhDSLMDg3QP10JKxgt8+i4G+CJLgSIs1ehjc++J5/wSN4UZKzyzJNAsD83lcx9KT1+meYEkYXt3Cq4BWqvL30B72Qjt8CozSbNdRqx2aHLUUB9Tbx8k4vgqSk+v0cYo3PXsN6vsd49oRas4+8Qaj3gfjuj3BwHip3YcAak06HpvX1h5uHC9Z9WM6B2A9Ae27N9pvDduwbrxGzSVzO/PklIqxGXO0i7oUbGRKkWdA6Uin0ZJHlYp7YndX7V3d3fWb8578tfAkpzcZwF3mqUftp845ZKTkvak05vVyJFQclyEpEYkorPRSFuU8ea+SrnfLzQx5UNxwDOoSc0+9OuYeas7kruktGLzvQ2P+I7joxoTPO6I/QsViMH2VomDzHSCBNwD8n0TnnWyo3hsoTpZmA1Ynx06j51EEJCGD8ZdW3fS99EBd0eJe+nm/h4rJ73QV3ukWMAcbhARpORcq+Ca828omH0q0TI1wjweM9+Z2eAhax5W4h7nLQCvOrEOA6i3eQx9nqa3jl5nxXIA2TcsLrBmbxPhgQad3LDhdjR1tBrLqyuPhTtT7CkquTtJ0NRjjSkN7CCzzoA4mUVL3L8FMCTfDchNuWHOPV1M9FmbkEws46Ye9HYiCzsSv8/HXfUGDm3LdwyPeEbmcVCUou0IOiWi3yl7fd4rsWAriCCWjCqJlf5jsiq/Nhx7p4Ovq1JFp9i8UjEHnUHrCrpOgBl0FCXgKVJ6lKffrhRQuQTV7GTi8lhYvh5rKuY5fTHltmTL43kfxjng9kNi/Sbk2Z7xL2uD2wPXvNGzAC6l5cYHO9qhts/tha+4DaTuYonrzInXOy/xr1I8Xfv/BOKcLzGQghfmshhlwJNTXTVvQn6T7uTDB82dBmi5PAfiLaMsT0c+DRjvXwYjOgQR/OzXHDkgwdb1M8UbFBNhsYfuZd8WQXkMFNtXgtEWnSY3Thy+5IPV8qxQQwNH8KquslYpDI6NecdNwrn9wKNc/MuIVOSzRjiVuTnghGili9DKdwCh8brHo36ek60/Ybea4cZkGEik8wqSdIGG/AQ75LMB7fAowqlDz7oZdchUG48kUr3PtMuxWk+bDXn2vVRcHNg2vceWtH46GWjyZvQVNYjOA158ehWfNmUAb1QG0LkpPgcrA/gwkMAP603j/Txvn51CvtKWMejULUfOOchOadJhCf08G4HPRj9WU5w5a/Wr2Gy9hfDdMk3+l9C1YWK1+M7SeE+CQ+h8F3Nm8gtRLLJuTFoISc59GelNqpAfvIK1Ry2bPrpag4dSLI5FRNZLYUVJwEa+V11lUAxQLwITzmRF0dgQ8f/tBas7r+uegQzHo9oI9cjS+s1dv55TOXY9OcDB4v07JrT10B+9HyQwOfXA4vTplUF0B+2sXSOhvT8GA1268D1K8/80car2cTVre2N0AqkqL8/n4Jajj4WgHdoi9Db+XcU7aRlUOpC+1kK7BJEFbN5jE5lIJZsuiFqDUqUo3WgzBwefHoYKz7+DMlLpK+AwOwJhgpxfP63yO52Ddxh44gUw6nszNrfSxMB0/dpkL0SMKASRrdAqcRFLEaV0CHJc4jvQuJDU4Rfx7IBLe6UDGAA4NmLpgNVgB1j9PYX5ZvC1WXKaR0m7GHfQBDADmkheDU3ooaZN1j8BGfSc6+zRKZs57HvbbEjgYyBjQROnpNlfCPqxQ+7zJQZt30TbhctynjHsFKfcYMwaeCxPAgbo7Oo7kGoWT7Iv4foAx8B+FybDEYkr7wbHzVAtvcKuUtjp/8+4GmATFG1KtadPPEx08I1DX2SQ5gp1LPNeK+dZ9wJyWW2aFNMwppv+AKVUcR4tZBnuY2+pQtmG9BhjN2yZ2XBfJrpONM0MvsVaHtb2LLXGiwAfsni7M49E+OHHYIi4NYzccLcBF01wtO6dcT1JPd/1215WfD4ItltLUa6OK7YvBwPbZeVCJJVz6D0ClvBxSQRiS0oPdei5sxsugQksMaOa4/wPv6JkU5zTSNpNdt2MxIIaNOdZbrQEpAIY8paerKeC301FHF6UOjaCM7w7AswTAZpXuVDiiLmkh7YpgTAOQ2LMwDaIZmB5N58MTfjVsvrsgkS/GPT5uaFHCsDM/jPvq+ejb0L46rcvBaNOlYKwXQJO5r4WKm6f0TbQc+Ag6dX8qW1IqcHL/vwd25hyYPDuj3nyvj1naX5fB7Ai/cRs+mCL5j8I87D1gTCfg+fepFw1yjcCIQKa4OayVO5EoVWfKIIgyGBZ0kFRj3Sw2rGqAVsTL5IIIsdEWsAAwGYuEgkAmm0pHBfjR0ru+3vpThZw8SvLWklsup2md4tw8ducdC7vlUgscg5jTPAMewHvRWjpptlZ1v23Ynl8wBu//wtP8cTifTG7+uNEjPkCwm3UegbNfbx0bNBwfaSrrEFT6Y63fFkDtq8E+3wsMhQAKVuu/3EL6aZX5ZFxn2vOsBppJyDg4YjGOf9maZjuF4n1pzPcZgS1skp5fDQCkS2A/f8SYWz6D0oM9ArTDaAvpO5CiwfwO9T7Hqvej0MB+bkU96TxZ0nrHr2Ia0fQSH0bNm3HxfPYnBB3/n/vR6tEHaKQWZT1s5XIRRiaKXGFw7oKxXU4/eWlp42D+qRdWdy10YZ829snC3EwjJZSWpBTby6bwFpSUtmb6CFap2bE0p6+6vKvLP9T3xYp2aLvobU9sFloV98yBW662OlhgIHfA0WSrZd2YrnkOks/B3OsowK1fai7KupSplj7YbrPBhddAklYNTrzAYACajboAj23Tbwvu/lxKffn4dpRMOKft2lU438Hz5kK6ldEuE8l51QvbuB/M4TmUVhJ5Eeq7CVNIaeCaizbSdRZ49xeoeZfAXfF+Q9BYNrVxIi2keLtPm0lvh3ZfxdLVGiu63v24P2sdgyZYoSpvg3rb46YI7WyF0ccFHFuId2PP89PqnhVBx13yt/Sn0btptE5hMnBhmR82eLmZ8sUNc+crwL53aUUB9lkF2H698kaXGJAWcBu/yaYIyAZgrekbXhigwPpwV2f9XUHgrAzGGSabC9iMMpqp5JHO5yRTUsMQpWREjGdM4RX2TMQ1ZYSAbStlMhxYYvJVbx5pT7/q6hQKkub2Va4rFPxTpRQDMsvblNFfNWADZJvQ4szc4Bn2KBCWdDo5PuedcOu+6A1tWNkM2tgURuYIEUUxBWQL8sZOVwhJjKRqT099aFZv9YKcJ78W5ivOKKO/esBKmW94iV3bwSRii0bIGMCS19FyQDFPsUjPU2hVdmViztXEf7xZu2hIbi1xg0bAhAhv67oBdXf71Ntbu6ar079IifFHiCiTqxllFALW58AHY87VlHjCSqSkbUdHVB3p1DuLYyVBHYfM375y2sio+66xsuvVaiIMTdQSWmegEKLJWm2oveyw4oXvnR21ek9X/WedXf4VnhvcquzVStZFGWWUUIllIbEiRxhzOolgOBFL4YK8fGRTb+mBh/eS+7/6sVsEuXcqoO1eqTl/Xx4Ti8sVbwnHClfqThgI4Qex4A6303GiRGz5nB/GAxeKwY0dxdothUJwi+fQE4F0quGGV5lczSgj2+nEgCXDsLTXFWvpGkSTqT1emYriW2PDnfLRJ3el17/2IRodo6pSgx8u5IOHOwrBt2RQ396XYmffp11839lOAXa2skFnhXh15JCSqAOeF6xRdupyR8hnHJenCeSY4HypkjKgZpRRaxsWKjHZXmLTeySwV45L1JE/S53zAnk1KnSU1SlOQ71F0ooxBcpn1ZnPKrX2JuEEIgp4wo7PIlzbE8hwHZ0j9fOCcIle1iEZZTSOSkzFph3r7AlS1mldhaae3HXk0jejCZ3WYjD+NdyVOQ5kTJwTe6OFyDoio4wmKGFlR2OpjTR3qsMZLFnZbu0rPE5Fl2NOs+mVjDLaSsQpTotRehhKrlnQK3M4XHFOcRl1ebw4e3XWZBlltDUBG8guLJmLl9HVsC6uI0c0r/g96s7x+s+VWXNllNFWt2GDQmOBqg+VuOgR9eaWU3f+bHXGT9XvtSx0IaOMZoYNmw/XrvG0TodL1JUvKYl6HhVcTmXyYgjkzHubUUYzRcJSf6j6dnmD1F04j3LOtbBVZSZVM8popgHWc26j7Tq/QY5zq5KkpQio2VxLRhnNRPp/AQYAb9SUyWDeZ5MAAAAASUVORK5CYII=",
              "fit": [125,150],
            }, {
              "width": "*",
              "alignment": "right",
              "text": "Omni Channel Campaign Report",
              "fontSize": 20,
              "font-weight": 200,
              "color":'#03357E',
              "margin": [5,2,10,20]
            }
        ],
      }
    }

/*
 layout.content.push({
      "columns": [{
        "width": "30%",
        "image": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOwAAABICAYAAAD1XhnsAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkVBNDcyN0I4MzNCMjExRTY4RDhBQzkwQTgxRDJFQThGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkVBNDcyN0I5MzNCMjExRTY4RDhBQzkwQTgxRDJFQThGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RUE0NzI3QjYzM0IyMTFFNjhEOEFDOTBBODFEMkVBOEYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RUE0NzI3QjczM0IyMTFFNjhEOEFDOTBBODFEMkVBOEYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7CldroAAAnWUlEQVR42uxd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",
        "fit": [125,150]
      }, {
        "width": "*",
        "alighnment": "right",
        "text": "Omni Channel Campaign Report",
        "fontSize": 20,
        "font-weight": 200,
        "color":'#03357E',
      }],
      "columnGap": 10
    });
*/

   layout.content.push({
	  "columns": [{
		  "width": "50%",
		  "text": "\nReport Date Range:\n",
		  "fontSize": 15,
	  }, {
		  "width": "50%",
		  "alignment": "right",
		  "text": "\nSales Order #:\n",
		  "fontSize": 15,
	  }],
	  "columnGap": 10
   });

   layout.content.push({
	  "columns": [{
		  "width": "50%",
		  "color": "#3B7B1E",
		  "fontSize": 11,
		  "stack": [
		        $(".form-group input").val()
          ]
	  }, {
		  "width": "50%",
		  "color": "#3B7B1E",
		  "alignment": "right",
		  "fontSize": 11,
		  "stack": [
	            inp
           ]
	  }],
	  "columnGap": 10
   });
   
   layout.content.push({
	   "text": "\n\nDigital Campaign Channel Data\n\n\n",
	   "color": "#808080",
	   "fontSize": 18,
	   "alignment": "center"
   });

  layout.content.push({
        "width": "100%",
        "image": charts["theme-area-chart-holder"].exportedImage,
        "fit": [500, 500]
      });

    layout.content.push({
      "text":"\n\n\n\n ",
    });

  layout.content.push({
  "fontSize": 12,
  "color":"#004889",
  "table": {
    // headers are automatically repeated if the table spans over multiple pages
    // you can declare how many rows should be treated as headers
    "headerRows": 1,
    "widths": ["35%", "35%", "*"],
    "body": [
      [{ text:"Channel Name", bold:true, fillColor:'#CCCCCC'}, {text:"Action(s) Count", bold:true, fillColor:'#CCCCCC'}, {text:"Impression(s) Count", bold:true, fillColor:'#CCCCCC'}],
      [{ text:"DISPLAY", bold:true}, $("#click_DISPLAY").html(), $("#impressions_DISPLAY").html()],
      [{ text:"DISPLAY-RETARGET", bold:true, fillColor:'#EEEEEE'}, {text:$("#click_DISPLAY-RETARGET").html(), fillColor:'#EEEEEE'}, {text:$("#impressions_DISPLAY-RETARGET").html(), fillColor:'#EEEEEE'}],
      [{ text:"SOCIAL", bold:true}, $("#click_SOCIAL").html(), $("#impressions_SOCIAL").html()],
      [{ text:"VIDEO", bold:true, fillColor:'#EEEEEE'}, {text:$("#click_VIDEO").html(), fillColor:'#EEEEEE'}, {text:$("#impressions_VIDEO").html(), fillColor:'#EEEEEE'}],
      [{ text:"TEXTAD", bold:true}, $("#click_TEXTAD").html(), $("#impressions_TEXTAD").html()],
      [{ text:"RICH_MEDIA", bold:true, fillColor:'#EEEEEE'}, {text:$("#click_RICH_MEDIA").html(), fillColor:'#EEEEEE'}, {text:$("#impressions_RICH_MEDIA").html(), fillColor:'#EEEEEE'}],
      [{ text:"EMAIL", bold:true}, $("#click_EMAIL").html(), $("#impressions_EMAIL").html()],
      [{ text:"TOTAL", bold:true, fillColor:'#CCCCCC'}, {text:$("#click_TOTAL").html(), fillColor:'#CCCCCC'}, {text: $("#impressions_TOTAL").html(), fillColor:'#CCCCCC'}]
    ]
  },
  pageBreak: 'after'
});

  // NEW PAGE
  
  layout.content.push({
	   "text": "\n\nAge & Gender Chart\n\n\n",
	   "color": "#808080",
	   "fontSize": 18,
	   "alignment": "center"
  });
  
  layout.content.push({
	  "columns": [{
		  "width": "50%",
   		  "image": charts["age_chart"].exportedImage,
   		  "fit": [240,225]
	  }, {
		  "width": "50%",
 		  "image": charts["users_gender"].exportedImage,
   		  "fit": [240,225]
	  }],
	  "columnGap": 10
  });
  
  layout.content.push({
	  "text": "\n\nTop 10 Placements\n\n",
	  "color": "#808080",
	  "fontSize": 18,
	  "alignment": "center"
  });

  layout.content.push({
    "width": "*",
    "image": charts["placement_chart"].exportedImage,
    "fit": [450, 550],
//    "pageBreak": 'after'
  });

  // NEW PAGE
/*
  layout.content.push({
      "text": "Impressions:",
      "color":'#808080',
      "fontSize": 15
    });

  layout.content.push({
        "width": "*",
        "image": charts["total_impressions"].exportedImage,
        "fit": [440, 400]
      });

  layout.content.push({
      "text": "\n\n \n\n Clicks:",
      "color":'#808080',
      "fontSize": 15
    });

    layout.content.push({
        "width": "*",
        "image": charts["total_clicks"].exportedImage,
        "fit": [440, 400]
      });
*/

    // Trigger the generation and download of the PDF
    // We will use the first chart as a base to execute Export on
    chart["export"].toPDF(layout, function(data) {
        var today = new Date();
        var day = today.toISOString().substring(0, 10);
      this.download(data, "application/pdf", "Combine_Report_" + day  + ".pdf");
    });

  }

});


function click_pagination() {
    $('.pagination a').off('click').on('click', function(e) {
         e.preventDefault();
         get_data_ajax($(this).attr('data-page'));

    });
}
click_pagination();

    $('#view_campaign').on('click', function(){
        $('.ad_id_list').parent().removeClass('active_ad');
        ad_id = 0;
        make_all_charts(start_date, end_date, ad_id);
        return false;
    });
    
    $(function () {

        $('#reportrange').daterangepicker({
            "startDate": js_data.start_date,
            "endDate": js_data.date_now,
            ranges: {
                'Hourly': [moment(), moment()],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        },
        function (start, end) {
            start_date = start.format('YYYY-MM-DD');
            end_date = end.format('YYYY-MM-DD'); //console.log(start_date, end_date);
            $('#form_start_date').val(start_date);
            $('#form_end_date').val(end_date);
            $('#form_reporting').submit();

            //make_all_charts(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), ad_id);
        });
    });

});

/**
 * 
 * @param date start_date
 * @param date end_date
 * @param int ad_id
 * make pie chart and line chart
 */

var make_all_charts = function(start_date, end_date, so){
    
    $.ajax({
        url: "/v2/campaign/chart_data1",
        type: "POST",
        beforeSend: function(){
            //show_loader($('#view_campaign'));
        },
        dataType: "json",
        data: {
            start_date: start_date,
            end_date: end_date,
            so : so,
        },

        success: function(msg)  {
            //console.log(msg);
            hide_loader();

            $("#table_all tbody").html("")
            if(msg.so_numbers.length != 0 ){
                if(msg.so_numbers.length > 1 ){
                    for (var i = 0; i < msg.so_numbers.length; i++) {
                        tr=$("<tr class='so_number'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_number').eq(i).append(td);
                        }     
                       a = $('<a href=reporting/'+(msg.so_numbers[i].id)+'></a>');
                       $('.so_number>td:first-child').eq(i).append(msg.so_numbers[i].io);
                       a.append(msg.so_numbers[i].name);
                       $('.so_number td:nth-child(2)').eq(i).append(a);
                       $('.so_number td:nth-child(3)').eq(i).append(msg.so_numbers[i].campaign_type);
                       $('.so_number td:last-child').eq(i).append(msg.so_numbers[i].campaign_status);
                    }
                }else{
                        tr=$("<tr class='so_number'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_number').append(td);
                        }     
                       a = $('<a href=reporting/'+(msg.so_numbers[0].id)+'></a>');
                       $('.so_number>td:first-child').append(msg.so_numbers[0].io);
                       a.append(msg.so_numbers[0].name);
                       $('.so_number td:nth-child(2)').append(a);
                       $('.so_number td:nth-child(3)').append(msg.so_numbers[0].campaign_type);
                       $('.so_number td:last-child').append(msg.so_numbers[0].campaign_status);
                }
            }
            if(msg.email_reporting && msg.email_reporting.length != 0 ){
                if(msg.email_reporting.length > 1 ){
                    for (var i = 0; i < msg.email_reporting.length; i++) {
                        tr=$("<tr class='so_email'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_email').eq(i).append(td);
                        }     
                       a = $('<a href=email_reporting/'+(msg.email_reporting[i].id)+'></a>');
                       $('.so_email>td:first-child').eq(i).append(msg.email_reporting[i].io);
                       a.append(msg.email_reporting[i].name);
                       $('.so_email td:nth-child(2)').eq(i).append(a);
                       $('.so_email td:nth-child(3)').eq(i).append(msg.email_reporting[i].campaign_type);
                       $('.so_email td:last-child').eq(i).append(msg.email_reporting[i].campaign_status);
                    }
                }else{
                        tr=$("<tr class='so_email'></tr>");
                        $(".bg-primary+tbody").append(tr);
                        for(var y=0;y<4;y++){
                           td = $('<td width="25%"></td>');
                           $('.so_email').append(td);
                        }     
                       a = $('<a href=email_reporting/'+(msg.email_reporting[0].id)+'></a>');
                       $('.so_email>td:first-child').append(msg.email_reporting[0].io);
                       a.append(msg.email_reporting[0].name);
                       $('.so_email td:nth-child(2)').append(a);
                       $('.so_email td:nth-child(3)').append(msg.email_reporting[0].campaign_type);
                       $('.so_email td:last-child').append(msg.email_reporting[0].campaign_status);
                }
             }

            $(".theme-pagination-wrap").css("display","block");
            highAreaChartsFire('theme-area-chart-holder', msg.click_data, false, so);

            if(msg.demograpics_data){
            var gender_array = [
                {data_filed: 'Male', click_count: msg.demograpics_data.male},  
                {data_filed: 'Female', click_count: msg.demograpics_data.female},  
                {data_filed: 'Unknown Gender', click_count: msg.demograpics_data.unknown_gender}  
            ];
            }else{
                var gender_array = [
                {data_filed: 'Unknown', click_count: 1}  
                ];
            }
            
            makeChartPie('users_gender', gender_array, '');

            if(typeof msg.places !== 'undefined' && msg.places.length > 0){
                var places_array = [];
                var obj = {};

                for(var i=0;i<msg.places.length;i++){
                    if(typeof obj[msg.places[i].placement] === 'undefined') {
                        obj[msg.places[i].placement] = parseInt(msg.places[i].impressions)
                    } else {
                        obj[msg.places[i].placement] =  obj[msg.places[i].placement] + parseInt(msg.places[i].impressions)
                    }
                }
                
                for(var k in obj) {
                    places_array.push({data_filed: k, click_count: obj[k]});
                }
            }else{
                var places_array = [
                    {data_filed: 'Unknown', click_count: 1},
                ];
            }

            makeChartPie('placement_chart', places_array, '');

            if(msg.demograpics_data){
                var age_array1 = [
                    {data_filed: '18-24', click_count: msg.demograpics_data['18_24']},
                    {data_filed: '25-34', click_count: msg.demograpics_data['25_34']},
                    {data_filed: '35-44', click_count: msg.demograpics_data['35_44']},
                    {data_filed: '45-54', click_count: msg.demograpics_data['45_54']},
                    {data_filed: '55-64', click_count: msg.demograpics_data['55_64']},
                    {data_filed: '64+', click_count: msg.demograpics_data['64+']},
                    {data_filed: 'Unknown Age', click_count: msg.demograpics_data.unknown_age},
                ];
            }else{
                var age_array1 = [
                {data_filed: 'Unknown', click_count: 1}  
                ];
            }

            makeChartPie('age_chart', age_array1, '');
        }
    });
};

/**
 * 
 * @param element div
 * @param object chartData
 * make line chart
 */


var highAreaChartsFire = function (div, chartData, format_dates, so) {
    var chart = AmCharts.makeChart(div, {
        "responsive": {
            "enabled": true
        },
        "export": {
            "enabled": true,
            "menu": []
          },
        "event": "rendered",
        "type": "serial",
        "theme": "light",
        "marginTop": 7,
        "dataProvider": chartData,
        "dataDateFormat":"YYYY-MM-DD",
        "valueAxes": [{
                "axisAlpha": 0.2,
                "dashLength": 1,
                "position": "left"
            }],
        "mouseWheelZoomEnabled": true,
        "graphs": [{
                "id": "g1",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Display clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "display_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a1",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Display retarget clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "display-retarget_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a2",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Email clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "email_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a3",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Rich media clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "rich_media_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a4",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Social clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "social_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a5",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Textad clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "textad_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a6",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Video clicks count: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "video_click_count",
                "useLineColorForBulletBorder": true
            },
            {
                "id": "a7",
                "balloonText": "[[category]]<br/><b><span style='font-size:12px;'>Total Impressions: [[value]]</span></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "impression_count",
                "useLineColorForBulletBorder": true
            }

        ],
        "chartCursor": {
        },
        "categoryField": "date",
        "categoryAxis": {
            "parseDates": format_dates,
            "axisColor": "#DADADA",
            "dashLength": 1,
            "minorGridEnabled": true
        },
        "listeners": [{
            "event": "rendered",
            "method": function(e) {
              var curtain = document.getElementById("curtain");
            }
          }]
        }, 2000);

    if(!so){
        chart.graphs.pop();  
    }else{
        var height_div = document.getElementById("theme-area-chart-holder");
        height_div.style.height = '365px';
    }

    chart.addListener("rendered", zoomChart);
    zoomChart();

// this method is called when chart is first inited as we listen for "rendered" event
    function zoomChart() {
        // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
        chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
    }
};

var highChartsFire = function () {
    var campaign_total_budget = $('#campaign_total_budget').text();
    campaign_total_budget = campaign_total_budget.slice(0, -1);
    var total_budget_spent = $('#total_budget_spent').text();
    total_budget_spent = total_budget_spent.slice(0, -1);
    total_budget_spent = parseFloat(total_budget_spent);
    campaign_total_budget = parseFloat(campaign_total_budget);

    if(!total_budget_spent) {
        total_budget_spent = 0;
    }
        var data = [
            {
                name: "Progress Spent",
                y: campaign_total_budget
            },
            {
                name: "Progress Left",
                y: total_budget_spent
            }
        ];

    // Create the chart
    $('#theme-piechart-holder').highcharts({
        chart: {
            renderTo: 'container',
            alignTicks: false,
            backgroundColor: '#fafafa',
            type: 'pie',
            margin: [0, 0, 0, 0],
            spacingTop: 0,
            spacingBottom: 0,
            spacingLeft: 0,
            spacingRight: 0
        },
        title: {
            text: ''
        },
        subtitle: {
            text: ''
        },
        credits: {
            enabled: false
        },
        plotOptions: {
            pie: {
                size:'100%',
                center: [80, 90],
                dataLabels: {
                    enabled: false
                },
                shadow: false,
            },
            series: {
                dataLabels: {
                    enabled: false,
                    format: '{point.name}: {point.y:.1f}%'
                },
                 states: {
                  hover: {
                    enabled: false,
                    halo: {
                      size: 0
                    }
                  }
                }
            }
        },
        exporting: {
            buttons: {
                contextButtons: {
                    enabled: false,
                    menuItems: null
                }
            },
            enabled: false
        },
        tooltip: {
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
        },
        series: [{
            name: "Brands",
            colorByPoint: true,
            data: data
        }]
    });
};
/**
 * 
 * @param element div
 * @param object chartData
 * @param string title
 * make pie chart 
 */

var makeChartPie = function(div, chartData, percent) {

    // if (chartData.length == 0){
    //     chartData = [{click_count : 1, data_filed: 'Unknown'}];
    // }
    
    var chart = AmCharts.makeChart(div, {
        "type": "pie",
           "export": {
            "enabled": true,
            "menu": []
          },
        "startDuration": 0,
        "theme": "light",
        "addClassNames": true,
        "legend": {
            "position": "bottom",
            "align":'center',
            "autoMargins": false
        },
        "innerRadius": "30%",
        "defs": {
            "filter": [{
                "id": "shadow", 
                "width": "200%",
                "height": "200%",
                "feOffset": {
                    "result": "offOut",
                    "in": "SourceAlpha", 
                    "dx": 0,
                    "dy": 0
                },
                "feGaussianBlur": {
                    "result": "blurOut",
                    "in": "offOut",
                    "stdDeviation": 5
                },
                "feBlend": {
                    "in": "SourceGraphic",
                    "in2": "blurOut",
                    "mode": "normal"
                }
            }]
        },
        "allLabels": [{
            "y": "44%",
            "align": "center",
            "size": 30,
            "bold": true,
            "text": percent,
            "color": "#555"
        }, ],
        "radius": "42%",
        "innerRadius": "60%",
        "dataProvider":chartData,
        "valueField": "click_count",
        "titleField": "data_filed"
    });

    if(div == 'total_impressions' || div == 'total_clicks'){
       chart.legend['enabled']=false;
    }

    chart.addListener("init", handleInit);

    chart.addListener("rollOverSlice", function(e) {
        handleRollOver(e);
    });

    function handleInit(){
        chart.legend.addListener("rollOverItem", handleRollOver);
    }

    function handleRollOver(e){

        if (e.dataItem.wedge){
            var wedge = e.dataItem.wedge.node;
            wedge.parentNode.appendChild(wedge); 
        } 
    }
};

var makeChartBar = function (div, chartData) {

    var chart = AmCharts.makeChart(div, {
        "type": "serial",
        "export": {
            "enabled": true,
            "menu": []
          },
        "theme": "light",
        "dataProvider": chartData,
        "valueAxes": [{
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0
            }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
                "balloonText": "[[category]]: <b>[[value]]%</b>",
                "fillAlphas": 0.6,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "clicks"
            }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "age",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20
        }

    });

};

var makeChartBarVideo = function (div, chartData) {


    var chart = AmCharts.makeChart(div, {

        "type": "serial",
        "export": {
            "enabled": true,
            "menu": []
          },
        "theme": "light",
        "dataProvider": chartData,
        "valueAxes": [{
            "gridColor": "#FFFFFF",
            "gridAlpha": 0.2,
            "dashLength": 0,

        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "[[category]]: <b>[[value]]%</b>",
            "fillAlphas": 0.6,
            "lineAlpha": 0.2,
            "type": "column",
            "valueField": "watch",
            "colorField": "color"

        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "video",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "tickPosition": "start",
            "tickLength": 20
        }

    });

};

var initialize_geo_map = function (msg){
    
    var latlngbounds = new google.maps.LatLngBounds();
    
    var mapOptions = {
          zoom: 5,
          center: new google.maps.LatLng(37.09024, -95.712891),
          mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        
    for(var city in msg.source_location)    {
        
        //$('#theme-geo-state-widget').find('.click_count_js:eq(' + city +')').text(msg.source_location[city].click_count);
                
        var circle = new google.maps.Circle({
            map: map,
            clickable: false,
            //radius: msg.source_location[city].radius * 2400,
            radius: (1609.34 * msg.source_location[city].radius),
            fillColor: '#ff0000',
            fillOpacity: 0.6,
            strokeColor: '#ff0000',
            strokeOpacity: .4,
            strokeWeight: .8
        });
        
        
        
        var latLng = new google.maps.LatLng(msg.source_location[city].latitude, msg.source_location[city].longitude);
                
        latlngbounds.extend(latLng);
        
        var markerCenter = new google.maps.Marker({
            position: latLng,
            title: "Location",
            map: map,
            draggable: false,
            i:city
        });
        
        markerCenter = new google.maps.Marker({map: map, position: latLng, clickable: true});
        
        markerCenter.set("id", city);
        
        var info = null;
        
        info = new google.maps.InfoWindow();
        
        //console.log(msg.source_location[i].click_count);
        
        
        
        google.maps.event.addListener(markerCenter, 'click', function() {
            
            var i = this.get('id');
            
            var contentString = '<p>State: ' + msg.source_location[i].state + '</p>' + 
                '<p>City: ' + msg.source_location[i].city + '</p>' +
                '<p>Click Count: ' + msg.source_location[i].click_count + '</p>';
            
            info.setContent(contentString);
            info.open(map, this);
        });

        circle.bindTo('center', markerCenter, 'position');
    }
    
    map.setCenter(latlngbounds.getCenter());
};

var initialize_country_map = function(country){
    
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
        center: new google.maps.LatLng(37.0625,-95.677068),
        zoom: 2,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    var geocoder = new google.maps.Geocoder();
    
    /*
    
    geocoder.geocode( {'address' : 'Canada'}, function(results, status) {
        console.log('----');
        console.log(results);
        console.log('---');
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
        }
    });
    */

    var world_geometry = new google.maps.FusionTablesLayer({
        query: {
            select: 'geometry',
            from: '1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk',
            where: "ISO_2DIGIT IN ('" + country + "')"
        },
        map: map,
        suppressInfoWindows: true
    });
};

var initialize_state_map = function(clicks_state){

    var polys = [];
    var labels = [];

    // Display the map, with some controls and set the initial location 
    var map = new GMap2(document.getElementById("map-canvas"));
    map.addControl(new GLargeMapControl());
    map.addControl(new GMapTypeControl());
    map.setCenter(new GLatLng(42.16, -100.72), 4);


    $('.click_count_js[data-state]').each(function() {

        var thisState = $(this).attr('data-state');
        $(this).text(clicks_state[thisState]);

    });


    GEvent.addListener(map, "click", function (overlay, point) {

        if (!overlay) {

            for (var i = 0; i < polys.length; i++) {
                if (polys[i].Contains(point)) {

                    var clicks_count = (clicks_state[labels[i].short_name] === undefined) ? 0 : clicks_state[labels[i].short_name];

                    map.openInfoWindowHtml(point, "<p>State: " + labels[i].label + "</p>Clicks: " + clicks_count);
                    i = 999; // Jump out of loop
                }
            }
        } 
    });



    // Read the data from states.xml
    var request = GXmlHttp.create();
    request.open("GET", "/v2/files/states.xml", true);
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            var xmlDoc = GXml.parse(request.responseText);
            // ========= Now process the polylines ===========
            var states = xmlDoc.documentElement.getElementsByTagName("state");


            // read each line
            for (var a = 0; a < states.length; a++) {

                var short_name = states[a].getAttribute("short_name");

                if (campaign.state.search(short_name) > -1 || short_name in clicks_state){

                    var label = states[a].getAttribute("name");

                    var colour = campaign.state.search(short_name) > -1 ? states[a].getAttribute("colour") : '#EADBC8';

                    // read each point on that line
                    var points = states[a].getElementsByTagName("point");
                    var pts = [];
                    for (var i = 0; i < points.length; i++) {
                        pts[i] = new GLatLng(parseFloat(points[i].getAttribute("lat")),
                                parseFloat(points[i].getAttribute("lng")));
                    }
                    var poly = new GPolygon(pts, "#000000", 1, 1, colour, 0.5, {clickable: false});

                    //google.maps.event.addListener(poly, 'click', showInfo);

                    polys.push(poly);
                    labels.push({label: label, short_name: short_name});
                    map.addOverlay(poly);

                }
            }


        }
    };
        
    request.send(null);
};
