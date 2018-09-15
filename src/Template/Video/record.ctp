<?= $this->Html->script(['https://cdn.WebRTC-Experiment.com/RecordRTC.js', 'https://cdn.webrtc-experiment.com/getScreenId.js'/*, 'https://cdn.webrtc-experiment.com/gumadapter.js'*/], ['block' => 'script']) ?>
<?= $this->Html->script(['https://webrtc.github.io/adapter/adapter-latest.js', 'https://cdn.webrtc-experiment.com/DetectRTC.js', 'https://cdn.webrtc-experiment.com/getHTMLMediaElement.js'], ['block' => 'script']) ?>
<?= $this->Html->script(['webrtc.js'], ['block' => 'bottomScripts']) ?>
<?= $this->Html->css(['https://cdn.webrtc-experiment.com/getHTMLMediaElement.css'], ['block' => 'css']) ?>

<?php $this->assign('title', __('Record your Review')); ?>


    <section class="experiment recordrtc text-center">
        <p><?= __('Welcome, please record your review and press on the save button when finished.') ?>
<!--
        <h2>
            <button class="btn btn-lg btn-primary">Start Recording</button>
        </h2>

        <div style="display: none;" class="text-center btn-group" role="group">
            <button class="btn btn-default" id="save-to-disk">Save To Disk</button>
            <button class="btn btn-default" id="open-new-tab">Open New Tab</button>
            <button class="btn btn-default" id="upload-to-server">Upload To Server</button>
        </div>

        <br /><br />

        <video controls muted></video>

        <br /><br /><br />
-->
        <style>
            .recordrtc, .recordrtc .header {
                display: block;
                text-align: center;
                padding-top: 0;
            }
            .recordrtc video, .recordrtc img {
                max-width: 100%!important;
                vertical-align: top;
            }
            .recordrtc audio {
                vertical-align: bottom;
            }
            .recordrtc option[disabled] {
                display: none;
            }
            .recordrtc select {
                font-size: 17px;
            }
        </style>

        <h2 class="header" style="text-align: center; display: none;">
            <select class="recording-media">
                <option value="record-audio-plus-video">Microphone+Camera</option>
                <option value="record-audio">Microphone</option>
                <option value="record-screen">Full Screen</option>
                <option value="record-audio-plus-screen">Microphone+Screen</option>
            </select>

            <span style="font-size: 15px;">into</span>

            <select class="media-container-format">
                <option>default</option>
                <option>vp8</option>
                <option>vp9</option>
                <option>h264</option>
                <option>mkv</option>
                <option>opus</option>
                <option>ogg</option>
                <option>pcm</option>
                <option>gif</option>
                <option>whammy</option>
            </select>

            <input type="checkbox" id="chk-timeSlice" style="margin:0;width:auto;" title="Use intervals based recording">
            <label for="chk-timeSlice" style="font-size: 15px;margin:0;width: auto;cursor: pointer;-webkit-user-select:none;user-select:none;" title="Use intervals based recording">Use timeSlice?</label>

            <br>

            <hr style="border-top: 0;border-bottom: 1px solid rgb(189, 189, 189);margin: 4px -12px;margin-top: 8px;">
            <select class="media-resolutions">
                <option value="default">Default resolutions</option>
                <option value="1920x1080">1080p</option>
                <option value="1280x720">720p</option>
                <option value="640x480">480p</option>
                <option value="3840x2160">4K Ultra HD (3840x2160)</option>
            </select>

            <select class="media-framerates">
                <option value="default">Default framerates</option>
                <option value="5">5 fps</option>
                <option value="15">15 fps</option>
                <option value="24">24 fps</option>
                <option value="30">30 fps</option>
                <option value="60">60 fps</option>
            </select>

            <select class="media-bitrates">
                <option value="default">Default bitrates</option>
                <option value="8000000000">1 GB bps</option>
                <option value="800000000">100 MB bps</option>
                <option value="8000000">1 MB bps</option>
                <option value="800000">100 KB bps</option>
                <option value="8000">1 KB bps</option>
                <option value="800">100 Bytes bps</option>
            </select>
        </h2>

        <button id="btn-start-recording" class="btn btn-primary btn-lg">Start Recording</button>
        <button id="btn-pause-recording" class="btn btn-warning btn-lg" style="display: none;">Pause</button>

        <br /><br />

        <div style="display: none;" class="text-center btn-group" role="group">
            <button class="btn btn-default" id="save-to-disk">Save To Disk</button>
            <button class="btn btn-default" id="upload-to-php">Submit Review</button>
            <button class="btn btn-default" id="open-new-tab">Open New Tab</button>
        </div>

        <div style="margin-top: 10px;" id="recording-player"></div>
    </section>
<!--
    <section class="experiment">
        <h2 class="header">
            URL Parameters
        </h2>
        <pre>
            // AUDIO
            <a href="?bufferSize=16384&sampleRate=44100">?bufferSize=16384&sampleRate=44100</a>
            <a href="?leftChannel=false&disableLogs=false">?leftChannel=false&disableLogs=false</a>

            // VIDEO
            <a href="?canvas_width=1280&canvas_height=720">?canvas_width=1280&canvas_height=720</a>
            <a href="?frameInterval=10">?frameInterval=10</a>
        </pre>
    </section>
-->
