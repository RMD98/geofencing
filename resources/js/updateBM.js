// var script = document.createElement('script');
// script.src = 'https://code.jquery.com/jquery-3.6.3.min.js'; // Check https://jquery.com/ for the current version
// document.getElementsByTagName('head')[0].appendChild(script);
// jQuery.ajax({
//     url:'/cronjob',
//     type:'GET',
//     success: function(){
//         alert('Success');
//     }
// })
const http = require('http');

http.get('http://localhost:8000/cronjob', (res) => {
    console.log(`Got response: ${res.statusCode}`);
    // Process the response data
  }).on('error', (err) => {
    console.error(`Got error: ${err.message}`);
  });