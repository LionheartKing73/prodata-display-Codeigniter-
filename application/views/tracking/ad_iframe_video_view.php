<script src="https://content.jwplatform.com/libraries/pFTCkdMP.js"></script>
<script>jwplayer.key="31g7gB9+3Ml/qV1DA9i/BtaSAATOAWIHjbVibA=="; </script>

<a href="<?php echo $destination_url; ?>" target="_blank"> <div id="media_video"></div> </a>
<script type="text/javascript">

jwplayer("media_video").setup({
 file: "<?php echo $ad['video_url'];?>",
 image: "<?php echo $ad['creative_url']; ?>",
 width: <?php echo $ad['creative_width']; ?>,
 height: <?php echo $ad['creative_height']; ?>,
 primary: "html5",
 advertising: { 
  client: "vast"
  }
});
</script>
