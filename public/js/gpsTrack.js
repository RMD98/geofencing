function geolocation(){
    navigator.geolocation.getCurrentPosition(position);
}

function position(pos){
    // map.flyTo({
    //     center:[pos.coords.longitude,pos.coords.latitude]
    // })
    // console.log(pos.timestamp);
    // setTimeout(geolocation,5000)
    return pos.coords.longitude,pos.coords.latitude;
}