<?php
$urlCurl = 'https://www.news-medical.net/tag/feed/Parkinsons-Disease.aspx';
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $urlCurl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET'
));

$response = curl_exec($curl);

curl_close($curl);
$xml = new SimpleXMLElement($response);
// echo "<pre>";
// print_r($xml);

?>
<div class="container-head">
    <section class="recentNews">
        <div class="">
            <h2 class="news-title">
                <?= $xml->channel->title; ?>
            </h2>
            <p class="news-sub-title"><?= $xml->channel->description; ?></p>
            <div class="row">
                <?php foreach ($xml->channel->item as $item) { ?>
                    <div class="ct-blog col-sm-6 col-md-4">
                        <div class="inner">

                            <div class="ct-blog-content">
                                <div class="ct-blog-date">
                                    <span><?= date('M, d', strtotime($item->pubDate)); ?></span>
                                    <span><?= date('h:i a', strtotime($item->pubDate)); ?></span>
                                </div>
                                <h3 class="ct-blog-header">
                                    <?= $item->title; ?>
                                </h3>
                            </div>
                            <div class="fauxcrop">
                                <a href="<?= $item->link; ?>">Link</a>
                                <br />
                                <?= $item->description; ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>