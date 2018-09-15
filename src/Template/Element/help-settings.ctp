<div id="help-settings-dialog" title="Tutorial">
    <div id="help-settings-carousel" class="carousel slide" data-ride="carousel" data-interval="false">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#help-settings-carousel" data-slide-to="0" class="active"></li>
            <li data-target="#help-settings-carousel" data-slide-to="1"></li>
            <li data-target="#help-settings-carousel" data-slide-to="2"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <?= $this->Html->image('help-slides/initial/initial-1.png') ?>
                <div class="carousel-caption">
                    <h2>Welcome to your dashboard!</h2>
                    <p>Let's get your account all set up.</p>
                    <p>This is your dashboard when you start your account. It's empty and alone.</p>
                    <p> Let's fix that! To get started, click on Settings.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/initial/initial-2.png') ?>
                <div class="carousel-caption">
                    <h3>Select your display style</h3>
                    <p>Start at the General tab.</p>
                    <p>Enter a title which will be displayed above your plugin. Usually something like Read Our Reviews.</p>
                    <p>Then select float from the Type dropdown menu.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/initial/initial-3.png') ?>
                <div class="carousel-caption">
                    <h3>Hide sources you won't use</h3>
                    <p>Most clients won't use AVVO. Simply click the tab and un-check the "Show?" box.</p>
                    <p>Do this for any sources you do not want on your plugin.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-1.png') ?>
                <div class="carousel-caption">
                    <h2>Setup Google Reviews</h2>
                    <p>On the Revued dashboard, click Settings and then the Google+ tab.</p>
                    <p>Keep this window open in your browser. Open a new window or tab and go to <a href="https://console.developers.google.com/apis/" target="_blank">Google's Developer API page</a> and login with your Google account.</p>
                    <p>You may need to create a project, simply name it as you like and proceed to the next step.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-2.png') ?>
                <div class="carousel-caption">
                    <h3>Generate Credentials</h3>
                    <p>Click APIS &amp; Service.</p>
                    <p>Click Credentials.</p>
                    <p>Click Create.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-3.png') ?>
                <div class="carousel-caption">
                    <p>Click API key.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-4.png') ?>
                <div class="carousel-caption">
                    <p>Copy the key (CTRL+C) and click CLOSE.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-5.png') ?>
                <div class="carousel-caption">
                    <p>Return to this window.</p>
                    <p>Paste the first key in the box highlighted in yellow here.</p>
                    <p>Repeat the process and paste the second key in the box highlighted in green here.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-6.png') ?>
                <div class="carousel-caption">
                    <h3>Google+ Page Setup</h3>
                    <p>Time to set up your Google+ page. If you already have a page proceed to the next step.</p>
                    <p>Open <a href="https://plus.google.com" target="_blank">Google Plus</a> in a new tab.</p>
                    <p>On the left side of the screen, at the bottom, click Brands.</p>
                    <p>On the next screen click CREATE GOOGLE+ PAGE.</p>
                    <p>Enter your company name and click Create.</p>
                    <p>On the next screen click Enable.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-7.png') ?>
                <div class="carousel-caption">
                    <p>After you've created the Google+ page, close the explore window that pops up and copy a section of the URL at the top of the page.</p>
                    <p>Make sure to stop at the end of the number as shown in the image.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/google/google-8.png') ?>
                <div class="carousel-caption">
                    <p>Return to this tab and paste the address in the box highlighted in yellow here.</p>
                    <p>Enter your business name in the URL and Plus Page boxes highlighted in green here.</p>
                    <p>Click Save.</p>

                    <h2>CONGRATULATIONS!</h2>
                    <p>You have completed the setup for Google!</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-1.png') ?>
                <div class="carousel-caption">
                    <h2>Setup Facebook Reviews</h2>
                    <p>On the Revued dashboard, click Settings and then the Facebook tab.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-2.png') ?>
                <div class="carousel-caption">
                    <p>In a new window or tab go to <a href="https://developers.facebook.com/tools-and-support/" target="_blank">Facebook's Developer Tools &amp; Support Page</a>.</p>
                    <p>If you are not already registered as a developer you will be asked to register, shown in the image.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-3.png') ?>
                <div class="carousel-caption">
                    <p>Click Create App ID.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-4.png') ?>
                <div class="carousel-caption">
                    <p>Enter the information as required.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-5.png') ?>
                <div class="carousel-caption">
                    <h3>Generate Access Token</h3>
                    <p>Click Graph API Explorer.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-6.png') ?>
                <div class="carousel-caption">
                    <p>Click Get Token.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-7.png') ?>
                <div class="carousel-caption">
                    <p>Click Get Page Access Token. Select your business page.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-8.png') ?>
                <div class="carousel-caption">
                    <p>The Access Token will propagate in the field highlighted on the image in green. Copy this string of text.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-9.png') ?>
                <div class="carousel-caption">
                    <p>Return to the Revued dashboard and paste the API access token in the appropriate box.</p>
                    <p>The correct box has been highlighted blue in the image.</p>
                    <p>Click Save.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-10.png') ?>
                <div class="carousel-caption">
                    <h3>Get Page ID</h3>
                    <p>Go to your businesses Facebook page.</p>
                    <p>Copy the part of the URL highlighted in red, as shown in the image. This is the Page ID.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/facebook/facebook-11.png') ?>
                <div class="carousel-caption">
                    <p>Finally paste the page ID from the URL of your Facebook page into the box highlighted on the image.</p>

                    <h2>CONGRATULATIONS!</h2>
                    <p>You have completed the setup for Facebook!</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-1.png') ?>
                <div class="carousel-caption">
                    <h2>Yelp Review Setup</h2>
                    <p>From the Revuse dashboard click the Yelp tab.</p>
                    <p>Keep this tab open for later.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-2.png') ?>
                <div class="carousel-caption">
                    <h3>Create App</h3>
                    <p>Open a new tab and go to <a href="https://www.yelp.com/developers/v3/manage_app/" target="_blank">Yelp's App Management page</a> and login with your Yelp account.</p>
                    <p>Name your app (something like Reviews), select Productivity as your industry and enter your email address.</p>
                    <p>Type "Pulling Reviews." for the description. Accept terms and conditions and click Create New App.</p>
                    <p>Click Manage App.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-3.png') ?>
                <div class="carousel-caption">
                    <h3>Get Client ID &amp; Client Secret</h3>
                    <p>On this screen click Show to reveal the Client Secret.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-4.png') ?>
                <div class="carousel-caption">
                    <p>You will need both the Client ID and the Client Secret.</p>
                    <p>First copy the Client ID.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-5.png') ?>
                <div class="carousel-caption">
                    <p>Paste the Client ID in the box highlighted yellow.</p>
                    <p>Return to the Yelp tab and copy the Client Secret. Paste it in the box highlighted green.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-6.png') ?>
                <div class="carousel-caption">
                    <h3>Get Business ID</h3>
                    <p>Back to Yelp, search for your business.</p>
                    <p>At the business page, copy the business ID from the URL. Highlighted in yellow on the image shown.</p>
                </div>
            </div>

            <div class="item">
                <?= $this->Html->image('help-slides/yelp/yelp-7.png') ?>
                <div class="carousel-caption">
                    <p>On the Revued dashboard, paste the business ID in the box. The correct box is highlighted in green on the image.</p>
                    <p>Click Save.</p>

                    <h2>CONGRATULATIONS!</h2>
                    <p>You have completed the setup for Yelp!</p>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#help-settings-carousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#help-settings-carousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>
