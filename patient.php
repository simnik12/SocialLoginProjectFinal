<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">

function execute(pageToken='') {
  var request = gapi.client.youtube.search.list({
    q: "Parkinsons Disease",
    part: 'snippet',
    pageToken: (pageToken != '') ? pageToken : "",
    maxResults: 5
  });

  request.execute(function(response) {
    $('#results').empty();
    var resultItems = response.result.items;
    $.each(resultItems, function(index, item) {
      videoTitle = item.snippet.title;
      html = '<li><h4>'+videoTitle+'</h4>';
      html += '<iframe width="560" height="315" src="https://www.youtube.com/embed/'+item.id.videoId+'" title="'+videoTitle+'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></li>';
      $('#results').append(html);
    });
    $('#results').append('<button class="btn btn-sucess" onclick="execute(\''+response.nextPageToken+'\');">Next Page</button>')
  });
}

function init() {
gapi.client.setApiKey("AIzaSyDTNqgmedmkdyTXCNzFub4DoNydAK_Qkp4");
    return gapi.client.load("https://www.googleapis.com/discovery/v1/apis/youtube/v3/rest")
    .then(function() {
            execute();
            console.log("GAPI client loaded for API");
        },
        function(err) {
            console.error("Error loading GAPI client for API", err);
        });

}

</script>
<script type="text/javascript" src="https://apis.google.com/js/client.js?onload=init"></script>
<ul id="results"></ul>
