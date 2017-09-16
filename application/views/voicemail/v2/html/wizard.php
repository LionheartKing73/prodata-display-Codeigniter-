{include file="v2/sections/header.php"}
<base href="{$base_url}">


<link href="{$base_url}/public/css/styles.css" rel="stylesheet" type="text/css"/>
<link href="{$base_url}/public/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" type="text/css" href="/v2/css/jquery.steps.css">
    <div class="theme-report-row-wrap">
        <div class="theme-container container-fluid">

            <div class="theme-report-campaign-list-row">

                <div class="theme-report-tabbed-section">
                    <nav class="theme-reoprt-tabbed-nav" role="navigation">
                        <form id="example-advanced-form" action="/v2/html/creat_campaign" >
                            
                            <h3>Monitor</h3>
                            <fieldset>
                                <h1>Select Your Campaign Type</h1> 
                                <div class="theme-report-tabbed-form-wrap">

                                    <div class="theme-tabbed-form-group">
                                        <input name="campaign_type" type="radio" value="EMAIL" class="theme-tabbed-form-control email-pays-campaign-radio" checked id="email-pays" />
                                        <label class="theme-tabbed-form-label" for="email-pays">Email to Pay-Per-Click Campaign</label>
                                    </div>
                                    <div class="theme-tabbed-form-group">
                                        <input name="campaign_type" type="radio" value="DISPLAY" class="theme-tabbed-form-control display-ads-radio" id="display-ads" />
                                        <label class="theme-tabbed-form-label" for="display-ads">Display Ads</label>
                                    </div>
                                    <div class="theme-tabbed-form-group">
                                        <input name="campaign_type" type="radio" value="DISPLAY-RETARGET" class="theme-tabbed-form-control marketing-ads-radio" id="remarketing" />
                                        <label class="theme-tabbed-form-label" for="remarketing">Display Ads + Remarketing</label>
                                    </div>
                                    <div class="theme-tabbed-form-group">
                                        <input name="campaign_type" type="radio" value="" class="theme-tabbed-form-control" id="link-ads" />
                                        <label class="theme-tabbed-form-label" for="link-ads">Text Link Ads (SEO)</label>
                                    </div>
                                    <div class="theme-tabbed-form-group">
                                        <input name="campaign_type" type="radio" value="TEXTAD" class="theme-tabbed-form-control" id="text-ads" />
                                        <label class="theme-tabbed-form-label" for="text-ads">Text Ads</label>
                                    </div>
                                    <div class="theme-tabbed-form-group theme-tabbed-form-submit-group">
                                        <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
                                    </div>

                                </div>
                            </fieldset>

                            <h3>Campaign Information</h3>
                            <fieldset>   
                                <div class="theme-tab-content theme-report-tab-content">

                                    <div class="theme-report-tabbed-form-wrap">

                                        <div class="theme-form-legend theme-display-table theme-no-gutter campaign_info">

                                            <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col">
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">IO # :</label>
                                                    <input name="io" type="text" value="" placeholder="IO # : 1233444" class="theme-geoform-control theme-form-control" style="text-transform: uppercase" />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Campaigne Name :</label>
                                                    <input name="name" type="text" value="" placeholder="Campaigne Name" class="theme-geoform-control theme-form-control" />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Campaign Vertical :</label>
                                                    <select name="vertical" class="theme-geoform-control theme-form-control">
                                                        <option value="" disabled="disabled" selected>Campaign Vertical</option>
                                                        {foreach from=$vertical_list item=vertical}
                                                        {$value = $vertical["remarketing_list_id"]}
                                                        {$option = $vertical["vertical"]}
                                                        <option value= "{$value}">  {ucfirst($option)}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Domain Name :</label>
                                                    <input name="domain" type="text" value="" placeholder="Domain Name" class="theme-geoform-control theme-form-control" />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Start Date :</label>
                                                    <input name="campaign_start_datetime" type="text" value="" placeholder="01-15-2015" class="start_date_datepicker  theme-geoform-control theme-form-control  theme-date-picker " />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Daily Budgets :</label>
                                                    <input name="budget" type="text" value="" placeholder="Daily Budgets" class="theme-geoform-control theme-form-control" />
                                                </div>
                                            </div>

                                            <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-form-half-col enable-campaign-criteria-col">
                                                <div class="theme-geoform-group theme-form-group theme-inline-group enable-campaign-criteria">
                                                    <input name="more_options" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="marketing-options">
                                                    <label for="marketing-options" class="theme-inline-label theme-custom-label theme-light-weight">Enable Campaign End Criteria? :</label>
                                                </div>

                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Maximum Impressions :</label>
                                                    <input id="max_impressions" name="max_impressions" type="text" value="" placeholder="Maximum Impressions" class="theme-geoform-control theme-form-control" />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Maximum Budget :</label>
                                                    <input name="max_budget" type="text" value="" placeholder="Maximum Budget" class="theme-geoform-control theme-form-control" />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">Maximum Clicks :</label>
                                                    <input id="max_clicks" name="max_clicks" type="text" value="" placeholder="Maximum Clicks" class="theme-geoform-control theme-form-control" />
                                                </div>
                                                <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                    <label class="theme-inline-label theme-light-weight">End Date :</label>
                                                    <input name="campaign_end_datetime" type="text" value="" placeholder="01-15-2015" class="end_date_datepicker theme-geoform-control theme-form-control  theme-date-picker " />
                                                </div>
                                            </div>

                                        </div>

                                        <div class="theme-form-group theme-submit-group theme-align-center">
                                            <a href="javascript:" class="btn_previous_step" >BACK</a>
                                            <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
                                        </div>
                                        
                                    </div>

                                </div>
                            </fieldset>

                            <h3>Digital Rooftop</h3>
                            <fieldset>
                                <div class="theme-tab-content theme-report-tab-content digitel_rooftop">

                                    <div class="theme-gelocation-from-row">

                                        <div class="theme-display-table theme-no-gutter">

                                            <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                                                <div class="theme-geolocation-form-wrap">

                                                    <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                                        <h2>Select Geo-Location Type</h2>
                                                        <div class="theme-form-radio-group location_type">
                                                            <input name="geo_type" type="radio" value="country" class="theme-geofrom-control theme-tabbed-form-control geo-country-radio" id="country" checked />
                                                            <label for="country" class="theme-geoform-label theme-tabbed-form-label">Country (Nationalwide)</label>
                                                            <input name="geo_type" type="radio" value="state" class="theme-geofrom-control theme-tabbed-form-control geo-state-radio" id="state" />
                                                            <label for="state" class="theme-geoform-label theme-tabbed-form-label">State</label>
                                                            <input name="geo_type" type="radio" value="postalcode" class="theme-geofrom-control theme-tabbed-form-control geo-postal-radio" id="postal-code" />
                                                            <label for="postal-code" class="theme-geoform-label theme-tabbed-form-label">Postal Code</label>
                                                        </div>
                                                    </div>

                                                    <div id="geo-country" class="theme-geoform-group theme-form-group">
                                                        <div class="theme-geofrom-selectbox">
                                                            <label for="">Country (Nationalwide)</label>
                                                            <select id="geo-country" name="country" class="theme-form-control theme-control">
                                                                <option value="">Select Country</option>
                                                                <option value="AF">Afghanistan</option>
                                                                <option value="AX">Åland Islands</option>
                                                                <option value="AL">Albania</option>
                                                                <option value="DZ">Algeria</option>
                                                                <option value="AS">American Samoa</option>
                                                                <option value="AD">Andorra</option>
                                                                <option value="AO">Angola</option>
                                                                <option value="AI">Anguilla</option>
                                                                <option value="AQ">Antarctica</option>
                                                                <option value="AG">Antigua and Barbuda</option>
                                                                <option value="AR">Argentina</option>
                                                                <option value="AM">Armenia</option>
                                                                <option value="AW">Aruba</option>
                                                                <option value="AU">Australia</option>
                                                                <option value="AT">Austria</option>
                                                                <option value="AZ">Azerbaijan</option>
                                                                <option value="BS">Bahamas</option>
                                                                <option value="BH">Bahrain</option>
                                                                <option value="BD">Bangladesh</option>
                                                                <option value="BB">Barbados</option>
                                                                <option value="BY">Belarus</option>
                                                                <option value="BE">Belgium</option>
                                                                <option value="BZ">Belize</option>
                                                                <option value="BJ">Benin</option>
                                                                <option value="BM">Bermuda</option>
                                                                <option value="BT">Bhutan</option>
                                                                <option value="BO">Bolivia, Plurinational State of</option>
                                                                <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                                                                <option value="BA">Bosnia and Herzegovina</option>
                                                                <option value="BW">Botswana</option>
                                                                <option value="BV">Bouvet Island</option>
                                                                <option value="BR">Brazil</option>
                                                                <option value="IO">British Indian Ocean Territory</option>
                                                                <option value="BN">Brunei Darussalam</option>
                                                                <option value="BG">Bulgaria</option>
                                                                <option value="BF">Burkina Faso</option>
                                                                <option value="BI">Burundi</option>
                                                                <option value="KH">Cambodia</option>
                                                                <option value="CM">Cameroon</option>
                                                                <option value="CA">Canada</option>
                                                                <option value="CV">Cape Verde</option>
                                                                <option value="KY">Cayman Islands</option>
                                                                <option value="CF">Central African Republic</option>
                                                                <option value="TD">Chad</option>
                                                                <option value="CL">Chile</option>
                                                                <option value="CN">China</option>
                                                                <option value="CX">Christmas Island</option>
                                                                <option value="CC">Cocos (Keeling) Islands</option>
                                                                <option value="CO">Colombia</option>
                                                                <option value="KM">Comoros</option>
                                                                <option value="CG">Congo</option>
                                                                <option value="CD">Congo, the Democratic Republic of the</option>
                                                                <option value="CK">Cook Islands</option>
                                                                <option value="CR">Costa Rica</option>
                                                                <option value="CI">Côte d'Ivoire</option>
                                                                <option value="HR">Croatia</option>
                                                                <option value="CU">Cuba</option>
                                                                <option value="CW">Curaçao</option>
                                                                <option value="CY">Cyprus</option>
                                                                <option value="CZ">Czech Republic</option>
                                                                <option value="DK">Denmark</option>
                                                                <option value="DJ">Djibouti</option>
                                                                <option value="DM">Dominica</option>
                                                                <option value="DO">Dominican Republic</option>
                                                                <option value="EC">Ecuador</option>
                                                                <option value="EG">Egypt</option>
                                                                <option value="SV">El Salvador</option>
                                                                <option value="GQ">Equatorial Guinea</option>
                                                                <option value="ER">Eritrea</option>
                                                                <option value="EE">Estonia</option>
                                                                <option value="ET">Ethiopia</option>
                                                                <option value="FK">Falkland Islands (Malvinas)</option>
                                                                <option value="FO">Faroe Islands</option>
                                                                <option value="FJ">Fiji</option>
                                                                <option value="FI">Finland</option>
                                                                <option value="FR">France</option>
                                                                <option value="GF">French Guiana</option>
                                                                <option value="PF">French Polynesia</option>
                                                                <option value="TF">French Southern Territories</option>
                                                                <option value="GA">Gabon</option>
                                                                <option value="GM">Gambia</option>
                                                                <option value="GE">Georgia</option>
                                                                <option value="DE">Germany</option>
                                                                <option value="GH">Ghana</option>
                                                                <option value="GI">Gibraltar</option>
                                                                <option value="GR">Greece</option>
                                                                <option value="GL">Greenland</option>
                                                                <option value="GD">Grenada</option>
                                                                <option value="GP">Guadeloupe</option>
                                                                <option value="GU">Guam</option>
                                                                <option value="GT">Guatemala</option>
                                                                <option value="GG">Guernsey</option>
                                                                <option value="GN">Guinea</option>
                                                                <option value="GW">Guinea-Bissau</option>
                                                                <option value="GY">Guyana</option>
                                                                <option value="HT">Haiti</option>
                                                                <option value="HM">Heard Island and McDonald Islands</option>
                                                                <option value="VA">Holy See (Vatican City State)</option>
                                                                <option value="HN">Honduras</option>
                                                                <option value="HK">Hong Kong</option>
                                                                <option value="HU">Hungary</option>
                                                                <option value="IS">Iceland</option>
                                                                <option value="IN">India</option>
                                                                <option value="ID">Indonesia</option>
                                                                <option value="IR">Iran, Islamic Republic of</option>
                                                                <option value="IQ">Iraq</option>
                                                                <option value="IE">Ireland</option>
                                                                <option value="IM">Isle of Man</option>
                                                                <option value="IL">Israel</option>
                                                                <option value="IT">Italy</option>
                                                                <option value="JM">Jamaica</option>
                                                                <option value="JP">Japan</option>
                                                                <option value="JE">Jersey</option>
                                                                <option value="JO">Jordan</option>
                                                                <option value="KZ">Kazakhstan</option>
                                                                <option value="KE">Kenya</option>
                                                                <option value="KI">Kiribati</option>
                                                                <option value="KP">Korea, Democratic People's Republic of</option>
                                                                <option value="KR">Korea, Republic of</option>
                                                                <option value="KW">Kuwait</option>
                                                                <option value="KG">Kyrgyzstan</option>
                                                                <option value="LA">Lao People's Democratic Republic</option>
                                                                <option value="LV">Latvia</option>
                                                                <option value="LB">Lebanon</option>
                                                                <option value="LS">Lesotho</option>
                                                                <option value="LR">Liberia</option>
                                                                <option value="LY">Libya</option>
                                                                <option value="LI">Liechtenstein</option>
                                                                <option value="LT">Lithuania</option>
                                                                <option value="LU">Luxembourg</option>
                                                                <option value="MO">Macao</option>
                                                                <option value="MK">Macedonia, the former Yugoslav Republic of</option>
                                                                <option value="MG">Madagascar</option>
                                                                <option value="MW">Malawi</option>
                                                                <option value="MY">Malaysia</option>
                                                                <option value="MV">Maldives</option>
                                                                <option value="ML">Mali</option>
                                                                <option value="MT">Malta</option>
                                                                <option value="MH">Marshall Islands</option>
                                                                <option value="MQ">Martinique</option>
                                                                <option value="MR">Mauritania</option>
                                                                <option value="MU">Mauritius</option>
                                                                <option value="YT">Mayotte</option>
                                                                <option value="MX">Mexico</option>
                                                                <option value="FM">Micronesia, Federated States of</option>
                                                                <option value="MD">Moldova, Republic of</option>
                                                                <option value="MC">Monaco</option>
                                                                <option value="MN">Mongolia</option>
                                                                <option value="ME">Montenegro</option>
                                                                <option value="MS">Montserrat</option>
                                                                <option value="MA">Morocco</option>
                                                                <option value="MZ">Mozambique</option>
                                                                <option value="MM">Myanmar</option>
                                                                <option value="NA">Namibia</option>
                                                                <option value="NR">Nauru</option>
                                                                <option value="NP">Nepal</option>
                                                                <option value="NL">Netherlands</option>
                                                                <option value="NC">New Caledonia</option>
                                                                <option value="NZ">New Zealand</option>
                                                                <option value="NI">Nicaragua</option>
                                                                <option value="NE">Niger</option>
                                                                <option value="NG">Nigeria</option>
                                                                <option value="NU">Niue</option>
                                                                <option value="NF">Norfolk Island</option>
                                                                <option value="MP">Northern Mariana Islands</option>
                                                                <option value="NO">Norway</option>
                                                                <option value="OM">Oman</option>
                                                                <option value="PK">Pakistan</option>
                                                                <option value="PW">Palau</option>
                                                                <option value="PS">Palestinian Territory, Occupied</option>
                                                                <option value="PA">Panama</option>
                                                                <option value="PG">Papua New Guinea</option>
                                                                <option value="PY">Paraguay</option>
                                                                <option value="PE">Peru</option>
                                                                <option value="PH">Philippines</option>
                                                                <option value="PN">Pitcairn</option>
                                                                <option value="PL">Poland</option>
                                                                <option value="PT">Portugal</option>
                                                                <option value="PR">Puerto Rico</option>
                                                                <option value="QA">Qatar</option>
                                                                <option value="RE">Réunion</option>
                                                                <option value="RO">Romania</option>
                                                                <option value="RU">Russian Federation</option>
                                                                <option value="RW">Rwanda</option>
                                                                <option value="BL">Saint Barthélemy</option>
                                                                <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
                                                                <option value="KN">Saint Kitts and Nevis</option>
                                                                <option value="LC">Saint Lucia</option>
                                                                <option value="MF">Saint Martin (French part)</option>
                                                                <option value="PM">Saint Pierre and Miquelon</option>
                                                                <option value="VC">Saint Vincent and the Grenadines</option>
                                                                <option value="WS">Samoa</option>
                                                                <option value="SM">San Marino</option>
                                                                <option value="ST">Sao Tome and Principe</option>
                                                                <option value="SA">Saudi Arabia</option>
                                                                <option value="SN">Senegal</option>
                                                                <option value="RS">Serbia</option>
                                                                <option value="SC">Seychelles</option>
                                                                <option value="SL">Sierra Leone</option>
                                                                <option value="SG">Singapore</option>
                                                                <option value="SX">Sint Maarten (Dutch part)</option>
                                                                <option value="SK">Slovakia</option>
                                                                <option value="SI">Slovenia</option>
                                                                <option value="SB">Solomon Islands</option>
                                                                <option value="SO">Somalia</option>
                                                                <option value="ZA">South Africa</option>
                                                                <option value="GS">South Georgia and the South Sandwich Islands</option>
                                                                <option value="SS">South Sudan</option>
                                                                <option value="ES">Spain</option>
                                                                <option value="LK">Sri Lanka</option>
                                                                <option value="SD">Sudan</option>
                                                                <option value="SR">Suriname</option>
                                                                <option value="SJ">Svalbard and Jan Mayen</option>
                                                                <option value="SZ">Swaziland</option>
                                                                <option value="SE">Sweden</option>
                                                                <option value="CH">Switzerland</option>
                                                                <option value="SY">Syrian Arab Republic</option>
                                                                <option value="TW">Taiwan, Province of China</option>
                                                                <option value="TJ">Tajikistan</option>
                                                                <option value="TZ">Tanzania, United Republic of</option>
                                                                <option value="TH">Thailand</option>
                                                                <option value="TL">Timor-Leste</option>
                                                                <option value="TG">Togo</option>
                                                                <option value="TK">Tokelau</option>
                                                                <option value="TO">Tonga</option>
                                                                <option value="TT">Trinidad and Tobago</option>
                                                                <option value="TN">Tunisia</option>
                                                                <option value="TR">Turkey</option>
                                                                <option value="TM">Turkmenistan</option>
                                                                <option value="TC">Turks and Caicos Islands</option>
                                                                <option value="TV">Tuvalu</option>
                                                                <option value="UG">Uganda</option>
                                                                <option value="UA">Ukraine</option>
                                                                <option value="AE">United Arab Emirates</option>
                                                                <option value="GB">United Kingdom</option>
                                                                <option value="US">United States</option>
                                                                <option value="UM">United States Minor Outlying Islands</option>
                                                                <option value="UY">Uruguay</option>
                                                                <option value="UZ">Uzbekistan</option>
                                                                <option value="VU">Vanuatu</option>
                                                                <option value="VE">Venezuela, Bolivarian Republic of</option>
                                                                <option value="VN">Viet Nam</option>
                                                                <option value="VG">Virgin Islands, British</option>
                                                                <option value="VI">Virgin Islands, U.S.</option>
                                                                <option value="WF">Wallis and Futuna</option>
                                                                <option value="EH">Western Sahara</option>
                                                                <option value="YE">Yemen</option>
                                                                <option value="ZM">Zambia</option>
                                                                <option value="ZW">Zimbabwe</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div id="geo-state" class="theme-geoform-group theme-form-group">
                                                        <div class="theme-geofrom-selectbox">
                                                            <label for="">State</label>
                                                            <select id="geo-state" name="state" class="theme-form-control theme-multi-selectbox theme-control" multiple>

                                                                <option value="AL">Alabama</option>
                                                                <option value="AK">Alaska</option>
                                                                <option value="AZ">Arizona</option>
                                                                <option value="AR">Arkansas</option>
                                                                <option value="CA">California</option>
                                                                <option value="CO">Colorado</option>
                                                                <option value="CT">Connecticut</option>
                                                                <option value="DE">Delaware</option>
                                                                <option value="DC">District Of Columbia</option>
                                                                <option value="FL">Florida</option>
                                                                <option value="GA">Georgia</option>
                                                                <option value="HI">Hawaii</option>
                                                                <option value="ID">Idaho</option>
                                                                <option value="IL">Illinois</option>
                                                                <option value="IN">Indiana</option>
                                                                <option value="IA">Iowa</option>
                                                                <option value="KS">Kansas</option>
                                                                <option value="KY">Kentucky</option>
                                                                <option value="LA">Louisiana</option>
                                                                <option value="ME">Maine</option>
                                                                <option value="MD">Maryland</option>
                                                                <option value="MA">Massachusetts</option>
                                                                <option value="MI">Michigan</option>
                                                                <option value="MN">Minnesota</option>
                                                                <option value="MS">Mississippi</option>
                                                                <option value="MO">Missouri</option>
                                                                <option value="MT">Montana</option>
                                                                <option value="NE">Nebraska</option>
                                                                <option value="NV">Nevada</option>
                                                                <option value="NH">New Hampshire</option>
                                                                <option value="NJ">New Jersey</option>
                                                                <option value="NM">New Mexico</option>
                                                                <option value="NY">New York</option>
                                                                <option value="NC">North Carolina</option>
                                                                <option value="ND">North Dakota</option>
                                                                <option value="OH">Ohio</option>
                                                                <option value="OK">Oklahoma</option>
                                                                <option value="OR">Oregon</option>
                                                                <option value="PA">Pennsylvania</option>
                                                                <option value="RI">Rhode Island</option>
                                                                <option value="SC">South Carolina</option>
                                                                <option value="SD">South Dakota</option>
                                                                <option value="TN">Tennessee</option>
                                                                <option value="TX">Texas</option>
                                                                <option value="UT">Utah</option>
                                                                <option value="VT">Vermont</option>
                                                                <option value="VA">Virginia</option>
                                                                <option value="WA">Washington</option>
                                                                <option value="WV">West Virginia</option>
                                                                <option value="WI">Wisconsin</option>
                                                                <option value="WY">Wyoming</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div id="geo-postal" class="theme-geoform-group theme-form-group geo-postal">
                                                        <div class="theme-geofrom-selectbox">
                                                            <p><label for="">Postal Code</label></p>
                                                            <div class="theme-inlineform-group zip_code">
                                                                <input name="zip" type="text" value="" placeholder="Enter your postal code" class="theme-form-control theme-geoform-control" />
                                                            </div>
                                                            <div class="theme-inlineform-group">
                                                                <select id="geo-postal-radius" name="radius" class="theme-form-control">
                                                                    <option value="">Select Radius</option>
                                                                    <option value="10">10-20</option>
                                                                    <option value="20">20-30</option>
                                                                    <option value="30">30-40</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="theme-report-socialsignal-wrap">

                                                        <div class="theme-geoform-group theme-form-group">
                                                            <div class="theme-geofrom-selectbox">
                                                                <!--  <label for="">Select Gender</label> -->
                                                                <select id="geo-gender" name="gender" class="theme-form-control theme-control">
                                                                    <option value="">Select Gender</option>
                                                                    <option value="male">Male</option>
                                                                    <option value="female">Female</option>
                                                                </select>
                                                                <br/>
                                                                <select id="geo-income-level" name="income_level" class="theme-form-control theme-control">
                                                                    <option value="">Select Income Level</option>
                                                                    <option value="">2000 or Higher</option>
                                                                    <option value="">3000 or Higher</option>
                                                                    <option value="">4000 or Higher</option>
                                                                </select>
                                                                <br/>
                                                                <select id="geo-chil-parent" name="parent" class="theme-form-control theme-control">
                                                                    <option value="">Select Children Parent</option>
                                                                    <option value="parent">Parent</option>
                                                                    <option value="not_parent">Not Parent</option>
                                                                </select>
                                                            </div>
                                                        </div>


                                                    </div>

                                                </div>
                                            </div>

                                            <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                                                <div id="theme-retargetting-section" class="theme-geolocation-form-wrap theme-retargetting-section">

                                                    <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                                        <h2>Remarketing Options</h2>
                                                    </div>

                                                    <div class="theme-bordered-legend theme-custom-field">
                                                        <div id="remarketing-campaign-group" class="theme-geoform-group theme-form-group theme-inline-group">
                                                            <input name="is_remarketing" type="checkbox" value="Y" class="theme-geoform-control theme-form-control" id="marketing-option" />
                                                            <label for="marketing-option" class="theme-inline-label theme-custom-label">Is Remarketing Campaign?</label>
                                                        </div>
                                                        <div id="theme-retargetting-group">
                                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                                <label class="theme-inline-label">Expanded Vertical Retargting</label>
                                                                <select id="retargetting" name="is_remarketing_io" class="theme-form-control">
                                                                    <option value="Y">Yes</option>
                                                                    <option value="N">No</option>
                                                                </select>
                                                            </div>
                                                            <div class="theme-geoform-group theme-form-group theme-inline-group" id="the-basics">
                                                                <label class="theme-inline-label">Linked Campaign(s)</label>
                                                                <input id="remarketing_io" name="remarketing_io" type="text" value="" placeholder="Linked Campaign(s)" class="theme-form-control theme-geoform-control typeahead">
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="theme-geolocation-form-wrap theme-mobile-carrer-row">

                                                    <div class="theme-geoform-group theme-tabbed-form-group them-form-group">
                                                        <h2>Mobile / Carrier Options</h2>
                                                    </div>

                                                    <div class="theme-geoform-group theme-form-group">


                                                        <select id="geo-device-type" name="device_type" class="theme-form-control theme-control">
                                                            <option value="">Select Device</option>
                                                            <option value="desktop">Desktop</option>
                                                            <option value="mobile">Mobile</option>
                                                            <option value="tablet">Tablet</option>
                                                        </select>
                                                        <br/>
                                                        <select id="geo-carrier" name="carrier" class="theme-form-control theme-control">
                                                            <option value="">Select Carrier</option>
                                                            <option value="">Any</option>
                                                            <option value="">Sprint</option>
                                                            <option value="">AT&T</option>
                                                            <option value="">Verizon</option>
                                                        </select>
                                                        <br/>
                                                        <select id="geo-preferred-mobile" name="preferred_mobile" class="theme-form-control theme-control">
                                                            <option value="tablate">Preferred Mobile</option>
                                                            <option value="">Any Property</option>
                                                            <option value="">Mobile Friendly</option>
                                                            <option value="">Desktop Friendly</option>
                                                            <option value="">In App</option>
                                                        </select>

                                                    </div>






                                                </div>

                                            </div>

                                        </div>

                                        <div class="theme-form-group theme-submit-group theme-align-center">
                                            <a href="javascript:" class="btn_previous_step" >BACK</a>
                                            <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
                                        </div>


                                    </div>

                                </div>
                            </fieldset>
                            
                            <h3>Creative</h3>
                            <fieldset>
                                <div class="theme-tab-content theme-report-tab-content">

                                    <div class="theme-textad-section">

                                        <h1>Create your text Ads</h1>

                                        <div class="theme-ad-banner-row theme-ad-creative-row">

                                            <div class="theme-display-table theme-no-gutter theme-no-gutter">

                                                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">

                                                    <div class="theme-ad-subrow theme-ad-banner-subrow">
                                                        <h1 class="theme-banner-row-title"><span>Example text Ads</span></h1>
                                                        <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                            <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                <figure>
                                                                    <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                </figure>
                                                            </div>
                                                            <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                <div class="theme-ad-content">
                                                                    <h2><a href="">You call to action Heading</a></h2>
                                                                    <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                    <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="theme-ad-subrow theme-ad-banner-subrow">
                                                        <h1 class="theme-banner-row-title"><span>Creative listing</span></h1>

                                                        <div class="theme-create-ad-form-wrap">

                                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                                <label class="theme-inline-label theme-light-weight">Title:</label>
                                                                <input name="title" type="text" value="" placeholder="Enter the title of your ad" class="theme-geoform-control theme-form-control" /><span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: 25 </span>
                                                                <span>
                                                            </div>

                                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                                <label class="theme-inline-label theme-light-weight">Display Url:</label>
                                                                <input name="display_url" type="text" value="" placeholder="Enter the display url of your ad" class="theme-geoform-control theme-form-control" />
                                                                <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: 255 </span>
                                                            </div>

                                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                                <label class="theme-inline-label theme-light-weight">URl:</label>
                                                                <input name="destination_url" type="text" value="" placeholder="Enter the url of your ad" class="theme-geoform-control theme-form-control" />
                                                                <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: 25 </span>
                                                            </div>

                                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                                <label class="theme-inline-label theme-light-weight">Description</label>
                                                                <textarea name="description_1" placeholder="Enter the desc of your ad" class="theme-geoform-control theme-form-control"></textarea>
                                                                <span style="font-size:12px; color: #999898; padding-left:143px;"> Character Left: 25 </span>
                                                            </div>

                                                            <div class="theme-geoform-group theme-form-group theme-inline-group">
                                                                <label class="theme-inline-label theme-light-weight">Keywords:</label>
                                                                <input name="keywords" type="text" value="" placeholder="Enter keywords for your ad" class="theme-geoform-control theme-form-control" />
                                                            </div>

                                                            <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                                                <input type="button" value="Create New Ad" class="theme-create-add-btn theme-submit-control">
                                                            </div>


                                                        </div>

                                                    </div>

                                                </div>


                                                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-sidebar-ad-col theme-table-top-cell">

                                                    <div class="theme-ad-subrow theme-ad-banner-subrow">
                                                        <h1 class="theme-banner-row-title"><span>text Ads creatives</span></h1>
                                                        <div class="theme-scrollable-ad-wrap theme-nicescroll-holder">

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                            <div class="theme-scrollable-ad-row">
                                                                <span class="theme-list-remove-icon closer"></span>
                                                                <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                    <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                        <figure>
                                                                            <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                        </figure>
                                                                    </div>
                                                                    <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                        <div class="theme-ad-content">
                                                                            <h2><a href="">You call to action Heading</a></h2>
                                                                            <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi..<strong>Click Now!!!</strong></p>
                                                                            <p class="theme-ad-url-line"> <a href="">www.mazdamarket.com</a></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="theme-ad-action-btn-group">
                                                                    <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                    <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                                <a href="" class="theme-btn theme-back-btn">Back</a>
                                                <input type="button" value="Continue" class="theme-cancel-btn theme-submit-control">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-for-email-pays-campaign">
                                        <div class="theme-ad-subrow theme-ad-banner-subrow">
                                                <!--<h1 class="theme-banner-row-title"><span>Creative listing</span></h1>-->

                                            <div class="theme-create-ad-form-wrap">

                                                <div class="theme-geoform-group theme-form-group theme-inline-group large">
                                                    <textarea placeholder="" class="theme-geoform-control theme-form-control"></textarea>
                                                    <p>Paste your html content above, then click next button</p>
                                                    <div class="theme-geoform-group theme-form-group theme-submit-group theme-align-right new-group">
                                                        <a id="show-hidden-section" href="" class="theme-create-add-btn theme-submit-control">NEXT</a>
                                                    </div>
                                                    

                                                </div>

                                                <div id="theme-campagin-hidden-section" class="theme-campagin-hidden-section">

                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Records:</label>
                                                        <input name="total_records" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />

                                                    </div>

                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">% Opens:</label>
                                                        <input name="percentage_opens" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />

                                                    </div>

                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">% Clicks:</label>
                                                        <input name="percentage_clicks" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />

                                                    </div>

                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">% Bounce:</label>
                                                        <input name="percentage_bounce" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />
                                                    </div>



                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Clicks:</label>
                                                        <input name="total_clicks" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />
                                                    </div>



                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Opens:</label>
                                                        <input name="total_opens" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />
                                                    </div>



                                                    <div class="theme-geoform-group theme-form-group theme-inline-group single-line-form">
                                                        <label class="theme-inline-label theme-light-weight single-line-form-lable">Total Bounce:</label>
                                                        <input name="total_bounce" type="text" value="" placeholder="" class="theme-geoform-control theme-form-control" />
                                                    </div>


                                                    <!--<div class="theme-geoform-group theme-form-group theme-submit-group theme-align-center">
                                                        <input type="button" value="Create New Ad" class="theme-create-add-btn theme-submit-control">
                                                    </div>-->

                                                    <hr/>
                                                    <table class="custom-itable">
                                                        <tr>
                                                            <td>Totals:</td>
                                                            <td>0</td>
                                                            <td>0%</td>
                                                        </tr>
                                                        <tr>
                                                            <td><h5><strong>Destination URL</strong></h5></td>
                                                            <td><h5><strong>Click Count</strong></h5></td>
                                                            <td><h5>%</h5></td>
                                                        </tr>
                                                        <tr style="background-color:#f8f8f8;">
                                                            <td>http://www.gardenstatehonda.com/?utm_source=eProfit&utm_medium=email&utm_campaign=eProfit</td>
                                                            <td><input type="text" value="0" placeholder="" class="theme-geoform-control theme-form-control" /></td>
                                                            <td><input type="text" value="0" placeholder="" style="padding:13px 15px; border:2px solid #dbdada; border-radius:3px; font-size:14px;"class="" />
                                                                %</td>
                                                        </tr>
                                                        <tr>
                                                            <td>http://www.gardenstatehonda.com/?utm_source=eProfit&utm_medium=email&utm_campaign=eProfit</td>
                                                            <td><input type="text" value="0" placeholder="" class="theme-geoform-control theme-form-control" /></td>
                                                            <td><input type="text" value="0" placeholder="" style="padding:13px 15px; border:2px solid #dbdada; border-radius:3px; font-size:14px;"class="" />
                                                                %</td>
                                                        </tr>
                                                        <tr style="background-color:#f8f8f8;">
                                                            <td>http://www.gardenstatehonda.com/?utm_source=eProfit&utm_medium=email&utm_campaign=eProfit</td>
                                                            <td><input type="text" value="0" placeholder="" class="theme-geoform-control theme-form-control" /></td>
                                                            <td><input type="text" value="0" placeholder="" style="padding:13px 15px; border:2px solid #dbdada; border-radius:3px; font-size:14px;"class="" />
                                                                %</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Totals:</td>
                                                            <td>0</td>
                                                            <td>0%</td>
                                                        </tr>
                                                    </table>
                                                    <div class="theme-form-group theme-submit-group theme-align-center">
                                                        <a href="javascript:" class="btn_previous_step" >BACK</a>
                                                        <a href="javascript:" class="btn_next_step btn_continue" >CONTINUE</a>
                                                    </div>
                                                </div>

                                            </div>



                                        </div>

                                    </div>
                                    <div class="theme-imagead-section">

                                        <h1>Create your Image ads</h1>

                                        <div class="theme-ad-banner-row theme-ad-creative-row">

                                            <div class="theme-display-table theme-no-gutter theme-no-gutter">

                                                <div id="theme-file-uploader" class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">
                                                    
                                                    <div class="col-sm-offset-4 col-sm-8 col-xs-12">
                                                        <div id="uploader">
                                                            <p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
                                                        </div>
                                                        {if $files_uploaded}
                                                            {foreach from=$files_uploaded item=file}
                                                            <div class="existing-img" title="{$file}">
                                                                <i class="fa fa-trash-o remove-img"></i>
                                                                <span>"{$file}"</span>
                                                            </div>
                                                            {/foreach}
                                                        {/if}
                                                        <div id="upload-result" class="alert-message"></div>
                                                    </div>

                                                </div>


                                                <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-sidebar-ad-col theme-table-top-cell">

                                                    <div class="theme-ad-subrow theme-ad-banner-subrow theme-imagead-banner-subrow">
                                                        <h1 class="theme-banner-row-title"><span>Uploaded Creative</span></h1>
                                                        <div id="ads_container" class="theme-scrollable-ad-wrap theme-nicescroll-holder">

                                                            


                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="theme-form-group theme-submit-group theme-align-center">
                                                <a href="javascript:" class="btn_previous_step" >BACK</a>
                                                <a href="javascript:" class="btn_next_step btn_continue btn_last_next" >CONTINUE</a>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </fieldset>
                            
                            <h3>Review</h3>
                            <fieldset>
                                <div class="theme-tab-content theme-report-tab-content">

                                    <h1>Review of your Campaign</h1>

                                    <div class="theme-ad-banner-row theme-ad-creative-row">

                                        <div class="theme-display-table theme-no-gutter theme-no-gutter">

                                            <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-table-top-cell">

                                                <div class="theme-summary-reivew-row">

                                                    <div class="theme-summy-subrow">
                                                        <h2>Summary of Campagain Monitor</h2>
                                                        <p id="campagain_monitor" > checked Type: <span></span> </p>
                                                        <p>Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi <br/><br/> Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi </p>
                                                    </div>

                                                    <div class="theme-summy-subrow">
                                                        <h2>Summary of Campagain Information</h2>
                                                        <p id="campaign_info_io" >IO: <span></span></p>
                                                        <p id="campaign_info_name" >Campaigne Name: <span></span></p>
                                                        <p id="campaign_info_vertical" >Campaign Vertical: <span></span></p>
                                                        <p id="campaign_info_domain_name" >Domain Name: <span></span></p>
                                                        <p id="campaign_info_start_date" >Start Date: <span></span></p>
                                                        <p id="campaign_info_daily_budget" >Daily Budgets: <span></span></p>
                                                        <p>Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi <br/><br/> Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi </p>
                                                    </div>

                                                    <div class="theme-summy-subrow">
                                                        <h2>Summary of Digital Rooftop</h2>
                                                        <p>Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi <br/><br/> Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi Browse for a new 2015-2016 Mazda in Albaraunique at Universi </p>
                                                    
                                                        <h4>GEO LOCATION</h4>
                                                        <div id="digital_rooftop" >
                                                            
                                                        </div>
                                                        <p id="gender" >Gender : <span></span></p>
                                                        <p id="inc_level" >Income Level : <span></span></p>
                                                        <p id="parent" >Parent : <span></span></p>
                                                        <p id="device" >Device : <span></span></p>
                                                        <p id="carrier" >Carrier : <span></span></p>
                                                        <p id="prefered" >Preferrd Mobile : <span></span></p>
                                                    
                                                    </div>

                                                </div>



                                            </div>


                                            <div class="theme-lg-6 theme-sm-6 theme-xs-12 theme-two-col theme-sidebar-ad-col theme-table-top-cell">

<!--                                                <div class="theme-ad-subrow theme-ad-banner-subrow">
                                                    <h1 class="theme-banner-row-title"><span>Creative Review</span></h1>
                                                    <div class="theme-scrollable-ad-wrap theme-nicescroll-holder" id="ads_finish_prev">

                                                        <div class="theme-scrollable-ad-row">
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <span class="theme-list-remove-icon closer"></span>
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="theme-ad-action-btn-group">
                                                                <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <span class="theme-list-remove-icon closer"></span>
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="theme-ad-action-btn-group">
                                                                <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <span class="theme-list-remove-icon closer"></span>
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="theme-ad-action-btn-group">
                                                                <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <span class="theme-list-remove-icon closer"></span>
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="theme-ad-action-btn-group">
                                                                <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <span class="theme-list-remove-icon closer"></span>
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="theme-ad-action-btn-group">
                                                                <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                            </div>
                                                        </div>

                                                        <div class="theme-scrollable-ad-row">
                                                            <span class="theme-list-remove-icon closer"></span>
                                                            <div class="theme-ad-banner-content theme-display-table theme-no-gutter">

                                                                <div class="theme-lg-3 theme-sm-5 theme-xs-12 theme-ad-logo-col theme-table-middle-cell">
                                                                    <figure>
                                                                        <a href=""><img src="/v2/images/report-template/no-ad-logo-thumb.png" alt="" /></a>
                                                                    </figure>
                                                                </div>
                                                                <div class="theme-lg-9 theme-sm-7 theme-xs-12 theme-ad-desc-col theme-table-middle-cell">
                                                                    <div class="theme-ad-content">
                                                                        <h2><a href="">You call to action Heading</a></h2>
                                                                        <p>Browse for a  new 2015-2016 Mazda in Albaraunique at Universi</p>
                                                                        <p class="theme-ad-url-line"> <span class="theme-click-url"> Click Now!!!</span><a href="">www.mazdamarket.com</a></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="theme-ad-action-btn-group">
                                                                <a id="edit-theme-ad" href="" class="edit-theme-ad">Edit</a>
                                                                <a id="url-theme-ad" href="" class="url-theme-ad">View Url</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>-->
                                                <div class="theme-review-large-image-row">
<!--                                                    <figure>-->
<!--                                                        <img src="/v2/images/report-template/review-banner.jpg" alt="" />-->
<!--                                                    </figure>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="theme-form-group theme-submit-group theme-align-center">
                                            <a href="javascript:" class="btn_previous_step" >BACK</a>
                                            <button id="finish_button" class="btn_next_step" >SAVE & LAUNCH</button>
                                        </div>
                                    </div>
                                </div>

                            </fieldset>
                        </form>
                    </nav>
                </div>

            </div>
        </div>
    </div>

{include file="v2/sections/scripts.php"}   


<script src="{$base_url}/public/jquery-ui/jquery-ui.js" type="text/javascript"></script>
 
<link href="{$base_url}/public/plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css"/>
<script src="{$base_url}/public/plupload/plupload.full.min.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/jquery.ui.plupload.js" type="text/javascript"></script>
<script src="{$base_url}/public/plupload/plupload_wizard.js" type="text/javascript"></script>

<script src="/v2/js/jquery.steps.min.js"></script> 
<script src="/v2/js/jquery.validate.min.js"></script>    

<script>
    $(document).ready(function(){
        
        /*ADD OPTIONS TO LAST FIELDSET*/
        $(".btn_last_next").on("click", function(){
            $("#campagain_monitor span").text($('#example-advanced-form-p-0 .theme-tabbed-form-group input[type="radio"]:checked + label').html());
            $("#campaign_info_io span").text($(".campaign_info .theme-geoform-group input[name=io]").val());
            $("#campaign_info_name span").text($(".campaign_info .theme-geoform-group input[name=name]").val());
            $("#campaign_info_vertical span").text($(".campaign_info .theme-geoform-group input[name=vertical]").val());
            $("#campaign_info_domain_name span").text($(".campaign_info .theme-geoform-group input[name=domain]").val());
            $("#campaign_info_start_date span").text($(".campaign_info .theme-geoform-group .hasDatepicker").val());
            $("#campaign_info_daily_budget span").text($(".campaign_info .theme-geoform-group input[name=budget]").val());
            
            if($('.location_type .theme-tabbed-form-group input[type="radio"]:checked').hasClass(".geo-country-radio")){
                $("#digital_rooftop").html($('.digitel_rooftop select[name=country] option:selected').text());
            }
            else if($('.location_type .theme-tabbed-form-group input[type="radio"]:checked').hasClass(".geo-postal-radio")) {
               $("#digital_rooftop").html($('.digitel_rooftop select[name=radius] option:selected').text());
               $("#digital_rooftop").append('<p>' + $('.digitel_rooftop input[name=zip]').text() + '</p>');
            }
            else {
                $("#digital_rooftop").html($('.digitel_rooftop select[name=country] option:selected').text());
                $("#digital_rooftop").append('<p>' +  $('.digitel_rooftop select[name=state] option:selected').text() + '</p>');
            }
            
            
            $("#gender span").text($('.digitel_rooftop select[name=gender] option:selected').text());
            $("#inc_level span").text($('.digitel_rooftop select[name=income_level] option:selected').text());
            $("#parent span").text($('.digitel_rooftop select[name=parent] option:selected').text());
            $("#device span").text($('.digitel_rooftop select[name=device_type] option:selected').text());
            $("#carrier span").text($('.digitel_rooftop select[name=carrier] option:selected').text());
            $("#prefered span").text($('.digitel_rooftop select[name=preferred_mobile] option:selected').text());
            
            
            
        });
        
        /*DATEPICKER*/
        $("body").on("change", ".start_date_datepicker", function(){
            var start_date = $(".start_date_datepicker").datepicker('getDate');
            if(start_date){
                start_date.setDate(start_date.getDate() + 1);
                $(".end_date_datepicker").datepicker("option", { minDate: start_date });
            }
        });
        
        /*NEXT PREV BUTTONS IN WIZARD CONTENT*/        
        $(".btn_continue").on("click", function(){        
            $('.actions li a[href="#next"]').trigger( "click" );
        });
        $(".btn_previous_step").on("click", function(){
            $('.actions li a[href="#previous"]').trigger( "click" );
        });
        
        /*REMOVE ADS FROM SIDEBAR*/
        $(document).on("click",".ads_remove", function(){
           $(this).parent(".theme-scrollable-ad-row").remove(); 
        });

        $(document).on( 'keypress', '[name="io"]', function(key){
            if((key.charCode>=65 && key.charCode<=90) || (key.charCode>=97 && key.charCode<=122) || (key.charCode>=48 && key.charCode<=57) || key.charCode==45 || key.charCode==32){
                if(key.charCode==32){
                    key.preventDefault();
                    $(this).val($(this).val()+"-");
                }
                return true;
            }
            return false;
        });

        $(document).on('change','select#geo-country',function(){
            if($(this).val()){
                $.post('/v2/html/get_states_by_country', { country : $(this).val() }, function(result){
                    var data = JSON.parse(result);
                    if(data.status == 'SUCCESS') {
                        var html = '';
                        $.each(data.states, function(key, value){
                            html +='<option value="'+value.state+'">'+value.name+'</option>';
                        });
                        $('select#geo-state').html(html);
                    } else {
                        //alert(data.message);
                    }
                });
            }
        });

        $(document).on('click','#finish_button',function(){
            console.log($('[role="menu"] li:last').children());
            $('[role="menu"] li:last').children().trigger('click');
        });

        $(document).on('click','.theme-ad-save',function(){
            var img = $(this).closest('.theme-scrollable-ad-row').children('figure').children('img').attr('src');
            var dest_url = $(this).prev().val();
            var valid = /^(ftp|http|https):\/\/[^ "]+$/.test(dest_url);
            if(!valid){
                $(this).prev().addClass('error');
                return false;
            }
            var ad = [creative_url=img, destination_url=dest_url];
            var ads = JSON.stringify(ad);
            $(this).prev().prev().val(ads);
//            var elem = $(this).closest('.theme-scrollable-ad-row').clone();
//            elem.find('.theme-ad-save').remove();
            //console.log(777);
            var html = '<div class="theme-scrollable-ad-row">'
                    //+ '<span class="theme-list-remove-icon closer ads_remove"></span>'
                + '<figure><img src="' + img +'" alt="" />'
                + '</figure><div class="theme-imagead-subrow-bottom">'
                + '<div class="theme-adbanner-form-group">'
                + '<input type="text" value="'+dest_url+'" class="theme-form-control theme-imagead-url-field" placeholder="Destination URL - http://..." />'
                + '</div></div></div>';
            $(".theme-review-large-image-row").append(html);
            $(this).remove();    

        });

        $(document).on('submit','#example-advanced-form', function(){
            return false;
        });
        
    });
    
    
var form = $("#example-advanced-form").show();
 
form.steps({
    headerTag: "h3",
    bodyTag: "fieldset",
    transitionEffect: "slideLeft",
    onStepChanging: function (event, currentIndex, newIndex)
    {
        // Allways allow previous action even if the current form is not valid!
        if (currentIndex > newIndex)
        {
            return true;
        }
        // Forbid next action on "Warning" step if the user is to young
        if (newIndex === 3 && Number($("#age-2").val()) < 18)
        {
            return false;
        }
        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex)
        {
            // To remove error styles
            form.find(".body:eq(" + newIndex + ") label.error").remove();
            form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }
        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onStepChanged: function (event, currentIndex, priorIndex)
    {
        // Used to skip the "Warning" step if the user is old enough.
        if (currentIndex === 2 && Number($("#age-2").val()) >= 18)
        {
            form.steps("next");
        }
//        // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
//        if (currentIndex === 2 && priorIndex === 3)
//        {
//            form.steps("previous");
//        }
    },
    onFinishing: function (event, currentIndex)
    {
        form.validate().settings.ignore = ":disabled";
        return form.valid();
    },
    onFinished: function (event, currentIndex)
    {   alert("Submitted!");

        var data = $("#example-advanced-form").serialize();
        //console.log(data);
        $.post('/v2/html/create_campaign', data, function(result){
            var data = JSON.parse(result);
            if(data.status == 'SUCCESS') {
//                var html = '';
//                $.each(data.states, function(key, value){
//                    html +='<option value="'+value.state+'">'+value.name+'</option>';
//                });
//                $('select#geo-state').html(html);
            } else {
//                alert(data.message);
            }
        });
    }
}).validate({
    errorPlacement: function errorPlacement(error, element) { element.before(error); },
    rules: {

        'campaign_type': {
            required: true,
        },

        'io': {
            required: true,
//            alphanumeric: true,
            maxlength : 11,
        },

        'name': {
            required: true,
            maxlength : 32,
        },

        'vertical': {
            required: true,
        },

        'domain': {
            required: false,
        },

        'campaign_start_datetime': {
            required: true,
        },

        'budget': {
            required: true,
            number : true,
            maxlength : 11,
        },

        'max_impressions': {
            required: function() {
                if($(".enable-campaign-criteria input:checkbox").is(":checked")){
                    return true;
                } else {
                    return false;
                }
            },
            number : true,
            maxlength : 11,
        },

        'max_clicks': {
            required: function() {
                if($(".enable-campaign-criteria input:checkbox").is(":checked")){
                    return true;
                } else {
                    return false;
                }
            },
            number : true,
            maxlength : 11,
        },

        'max_budget': {
            required: function() {
                if($(".enable-campaign-criteria input:checkbox").is(":checked")){
                    return true;
                } else {
                    return false;
                }
            },
            number : true,
            maxlength : 11,
        },

        'campaign_end_datetime': {
            required: function() {
                if($(".enable-campaign-criteria input:checkbox").is(":checked")){
                    return true;
                } else {
                    return false;
                }
            },
        },

        'country': {
            required:  function() {
                if($("#country").is(":checked") || $("#state").is(":checked")){
                    return true;
                } else {
                    return false;
                }
            },
        },

        'state': {
            required:  function() {
                return $("#state").is(":checked");
            },
        },

        'zip': {
            required:  function() {
                return $("#postal-code").is(":checked");
            },
        },

        'radius': {
            required:  function() {
                return $("#postal-code").is(":checked");
            },
        },

        'remarketing_io': {
            required:  function() {
                return $("input#marketing-option.theme-geoform-control.theme-form-control").is(":checked");
            },
        },

        'ads': {
            required: true,
        },
//
        'dest_url': {
            required: true,
            url: true,
        },
//
//        'io': {
//            required: true,
//        },
//
//        'io': {
//            required: true,
//        },
//
//        'io': {
//            required: true,
//        },
//
//        'io': {
//            required: true,
//        },






//        confirm: {
//            equalTo: "#password-2"
//        }
    }
});

</script>
</body>
</html>

