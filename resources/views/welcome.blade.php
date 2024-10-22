<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Business Casual - Start Bootstrap Theme</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
     
        <script src='https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.js'></script>
        <link href='https://api.mapbox.com/mapbox-gl-js/v2.4.1/mapbox-gl.css' rel='stylesheet' />

        <!-- Import mapbox geocoding API -->
        <!-- <script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js'></script> -->
        <!-- <link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css' type='text/css' /> -->
    </head>
    <body>
        <header>
            <h1 class="site-heading text-center text-faded d-none d-lg-block">
                <span class="site-heading-upper text-primary mb-3">A Free Bootstrap Business Theme</span>
                <span class="site-heading-lower">Business Casual</span>
            </h1>
        </header>
        <section class="page-section cta">
            <div class="container">
                <div class="row ">
                    <div class="col-md-3">
                        <input class="form-control" type="text" name="koord" id="koord">
                        <button type="button" onclick="check()" class="btn-secondary btn">Get Coord</button>
                        <button type="button" onclick="checkDist()" class="btn-secondary btn">Cek Distance</button>
                    </div>
                    <div class="col-md-4 center"id="mapbox" style='width: 1000px; height: 650px;'></div>
                </div>
            </div>
        </section>
        <footer class="footer text-faded text-center py-5">
            <div class="container"><p class="m-0 small">Copyright &copy; Your Website 2022</p></div>
        </footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        
        <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
        <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css">
        <!-- <script src="js/gpsTrack.js"></script> -->
        <script src="js/hitungTitik.js"></script>
        <script type="text/javascript">
            
            function center(long,lat){
                map.flyTo({
                    center:[long,lat]
                })
            }
            var latitude='';
            var longitude ='';
            let getLocationPromise = new Promise((resolve, reject) => {
                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {

                        // console.log(position.coords.latitude, position.coords.longitude) //test...

                        lat = position.coords.latitude
                        long = position.coords.longitude

                        // console.log("LATLONG1: ", lat, long) //test...

                        // Resolving the values which I need
                        resolve({latitude: lat, 
                                longitude: long})
                    })

                } else {
                    reject("your browser doesn't support geolocation API")
                }
            })
            const randId = Math.random().toString(36).substring(2,7);
            console.log('{{secure_url('')}}')
            function check(){
                // Now I can use the promise followed by .then() 
                // to make use of the values anywhere in the program
                let desc = document.getElementById('koord').value
                getLocationPromise.then((location) => {
                    // console.log(window.location.pathname)
                    $.ajax(
                            {
                                // url : "{{secure_url('')}}/add_koord",
                                url : "/add_koord",
                                type :"POST",
                                // dataType: "json",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data : {
                                    id : desc,
                                    lat : location.latitude,
                                    long :  location.longitude, 
                                },
                                success :function(){
                                    alert('Success');
                                }
                            }
                        )
                    
                }).catch((err) => {
                    console.log(err)
                })

                
            }
            function checkDist(){
                var dist = []
                var neigh = []
                getLocationPromise.then((location)=>{
                    let loc = [location.longitude, location.latitude]
                    benchmarkCoord.forEach((value,index,arr) => {
                        var jarak = hitungJarak(value,loc)
                        dist.push([benchmarkId[index],jarak])
                        // console.log('Jarak '+randId+' - '+benchmarkId[index]+' : ' + jarak)
                        dist.sort((a,b)=> a[1]-b[1])
                    });
                    neigh.push(dist.slice(0,2))
                    console.log(neigh)
                    checkBenchmark(neigh)
                })
            }
            function checkBenchmark(neighbour){
                var adj =[]
                neighbour[0].forEach((value,index,arr) => {
                    adj.push(benchmarkCoord[benchmarkId.indexOf(value[0])])
                    console.log(value)
                });
                var adjDist = []

                adj.forEach((val,i,arr) => {
                    adjDist.push(hitungJarak(centCoord,val))
                });
                getLocationPromise.then((location)=>{
                    let loc = hitungJarak(centCoord,[location.longitude,location.latitude])
                    if (loc > Math.max.apply(Math,adjDist)){
                        $.ajax(
                            {
                                // url : "{{secure_url('')}}/add_bm",
                                url : "/add_bm",
                                type :"POST",
                                // dataType: "json",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data : {
                                    lat : location.latitude,
                                    long :  location.longitude, 
                                },
                                success :function(){
                                    alert('Success');
                                }
                            }
                        )
                    }
                })
            }
            var benchmarkId =[]
            var benchmarkCoord=[]
            var centCoord = [] 
           
            
            // TO MAKE THE MAP APPEAR YOU MUST
            // ADD YOUR ACCESS TOKEN FROM
            // https://account.mapbox.com
            const at ='pk.eyJ1IjoiYXJrYW5mYXV6YW45MyIsImEiOiJja3U2djJtYjcycm00Mm5vcTh0bHJxMmh6In0.8p3Sy60ztY0-uY-UTZSFHQ';
            mapboxgl.accessToken = at;
            const map = new mapboxgl.Map({
                container: 'mapbox', // container ID
                style: 'mapbox://styles/mapbox/streets-v12', // style URL
                center: [107.63557468916463,-6.89633716939986], // starting position [lng, lat]
                zoom: 18, // starting zoom
            });
            
            

            const coordinatesGeocoder = function (query) {
                // for searchin place in map
                // Match anything which looks like
                // decimal degrees coordinate pair.
                const matches = query.match(
                    /^[ ]*(?:Lat: )?(-?\d+\.?\d*)[, ]+(?:Lng: )?(-?\d+\.?\d*)[ ]*$/i
                );
                if (!matches) {
                    return null;
                }
                
                function coordinateFeature(lng, lat) {
                    return {
                        center: [lng, lat],
                        geometry: {
                            type: 'Point',
                            coordinates: [lng, lat]
                        },
                        place_name: 'Lat: ' + lat + ' Lng: ' + lng,
                        place_type: ['coordinate'],
                        properties: {},
                        type: 'Feature'
                        };
                }
                
                const coord1 = Number(matches[1]);
                const coord2 = Number(matches[2]);
                const geocodes = [];
                
                if (coord1 < -90 || coord1 > 90) {
                // must be lng, lat
                    geocodes.push(coordinateFeature(coord1, coord2));
                }
                
                if (coord2 < -90 || coord2 > 90) {
                // must be lat, lng
                    geocodes.push(coordinateFeature(coord2, coord1));
                }
                
                if (geocodes.length === 0) {
                // else could be either lng, lat or lat, lng
                    geocodes.push(coordinateFeature(coord1, coord2));
                    geocodes.push(coordinateFeature(coord2, coord1));
                }
                
                return geocodes;
            };

            $.ajaxSetup({async:false});
            // $.get('{{secure_url('')}}/benchmark',{},function(data,status,jqXHR){
            $.get('/benchmark',{},function(data,status,jqXHR){
                for(i=0; i<=data.length-1;i++){
                    benchmarkId.push(data[i].koord_id);
                    benchmarkCoord.push([data[i].longitude,data[i].latitude]);
                    
                }
                centCoord = centroid(benchmarkCoord);
                center(centCoord[0],centCoord[1]);
                // console.log(benchmarkCoord);
            })
            $.ajaxSetup({async:true});
            
            let features =[];
            // console.log(benchmarkId.length)
            for(var j =0 ; j<benchmarkId.length;j++){
                features.push(
                    {
                        'type': 'Feature',
                        'properties' : {
                            'description' : benchmarkId[j],
                        },
                        'geometry':{
                            'type':'Point',
                            'coordinates': benchmarkCoord[j]
                        }
                    },
                );
            }
          
            map.on('load', () => {
        

                // Add a data source containing GeoJSON data.
                map.addSource('maine', {
                    'type': 'geojson',
                    'data': {
                        'type': 'Feature',
                        'geometry': {
                            'type': 'Polygon',
                            // These coordinates outline Maine.
                            'coordinates': [
                                benchmarkCoord
                            ]
                        }
                    }
                });
                map.addSource('point',{
                    'type': 'geojson',
                    'data': {
                        'type': 'FeatureCollection',
                        'features': features,
                    }
                })
                // Add a new layer to visualize the polygon.
                map.addLayer(
                    {
                        'id': 'maine',
                        'type': 'fill',
                        'source': 'maine', // reference the data source
                        'layout': {},
                        'paint': {
                            'fill-color': '#0080ff', // blue color fill
                            'fill-opacity': 0.5
                            }
                    },
                    
                );
                map.addLayer(
                    {
                        'id' : 'poi-labels',
                        'type': 'symbol',
                        'source': 'point',
                        'layout' : {
                            'text-field': ['get','description'],
                            'text-variable-anchor':['top','bottom','left','right'],
                            'text-radial-offset':0.5,
                            'text-justify': 'auto',
                        }    
                    }
                );
                // Add a black outline around the polygon.
                map.addLayer({
                    'id': 'outline',
                    'type': 'line',
                    'source': 'maine',
                    'layout': {},
                    'paint': {
                        'line-color': '#000',
                        'line-width': 3
                    }
                });

            });
            benchmarkId.forEach((value,index,element) => {
                let jarak = hitungJarak(centCoord,benchmarkCoord[index]);
                // console.log('Jarak center - '+value+' : ' + jarak)
            });
        </script>
    </body>
</html>
