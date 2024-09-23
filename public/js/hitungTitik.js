function hitungJarak(koordinatA,koordinatB){
    //menghitung jarak antara 2 titik
    let longA,longB,latA,latB = 0;

    longA = (koordinatA[0] * 3.14)/180;
    longB = (koordinatB[0] * 3.14)/180;
    latA = (koordinatA[1] * 3.14)/180;
    latB = (koordinatB[1] * 3.14)/180;
    
    
    deltaLat = Math.sin((latA - latB)/2);
    deltaLong = Math.sin((longA - longB)/2);
    jarak = 6371 * (2 * Math.asin(Math.sqrt(Math.pow(deltaLat,2) + (Math.cos(latA) * Math.cos(latB) * Math.pow(deltaLong,2)))))
    return (jarak * 1000);
}
function centroid(koord){
    x = 0;
    y = 0;
    for(let i=0; i<koord.length; i++){
        x += Number(koord[i][0]);
        y += Number(koord[i][1]);
    }
    console.log(x/koord.length,y/koord.length);
    return [x/koord.length,y/koord.length]
   }