<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/jpg" href="https://i.pinimg.com/236x/0b/de/ad/0bdead28e9d5945c687911b3e79cc373.jpg">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="http://fonts.cdnfonts.com/css/common-pixel" rel="stylesheet">
<title>./Hacked By Kamenkun</title>
<style>
body {
  margin: 0;
  padding: 0;
  background: url('https://media1.tenor.com/m/WmelMRML4jEAAAAC/over-the-garden-wall-the-beast.gif') no-repeat center center fixed; /* Replace with your background image URL */
  background-size: cover; /* Ensure the background covers the entire viewport */
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  text-align: center;
  color: white;
  font-family: 'Common Pixel', sans-serif;
  flex-direction: column; /* Align content vertically */
}

#text {
  color: red;
  font-size: 4vw; /* Use viewport width for responsive font size */
  text-transform: uppercase;
  font-weight: 900;
}

#text2 {
  color: white;
  font-size: 2vw; /* Use viewport width for responsive font size */
  margin-top: 5vh; /* Use viewport height for vertical spacing */
  text-transform: uppercase;
  font-weight: 900;
}

img {
  width: 80%; /* Make image responsive */
  max-width: 400px; /* Limit the maximum width */
  height: auto; /* Maintain aspect ratio */
  padding: 10px;
}

@media (max-width: 768px) {
  #text {
    font-size: 6vw; /* Increase font size for smaller screens */
  }

  #text2 {
    font-size: 3vw; /* Increase font size for smaller screens */
  }
}

@font-face {
  font-family: 'Common Pixel';
  font-style: normal;
  font-weight: 400;
  src: local('Common Pixel'), url('https://fonts.cdnfonts.com/s/7553/COMMP___.woff') format('woff');
}

marquee {
  color: red;
  font-size: 1.5vw; /* Small font size */
  font-weight: 900;
  width: 100%; /* Full width */
  position: absolute;
  bottom: 0; /* Stick to the bottom */
  font-family: 'Grenze Gotisch', cursive; /* Set font to Arial */
}
</style>
</head>
<body>


<div id="text"></div>

<script>
var i = 0, text;
text = "Hacked By ./KamenKun";
var direction = 1; // Direction of typing: 1 for typing, -1 for deleting

function ketik() {
  if (direction === 1) {
    if (i <= text.length) {
      document.getElementById("text").innerHTML = text.substring(0, i++);
    } else {
      direction = -1; // Change direction to deleting
    }
  } else {
    if (i >= 0) {
      document.getElementById("text").innerHTML = text.substring(0, i--);
    } else {
      direction = 1; // Change direction to typing
    }
  }
  setTimeout(ketik, 100); // Adjust typing speed
}
ketik();
</script>
<p>
<p>
<center><a href="https://twitter.com/kamenkun1337?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">Follow @kamenkun1337</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></center>
<marquee>Copyright © 2019 - 2024  Coded by ./KamenKun </marquee>
</body>
</html>
