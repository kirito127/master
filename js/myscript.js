function generateRandom(min, max, length) {
    var output = '';
    for (var i = 0; i < length; i++) {
        output += Math.floor(Math.random() * (max - min + 1) + min);
    }
    return output;
}