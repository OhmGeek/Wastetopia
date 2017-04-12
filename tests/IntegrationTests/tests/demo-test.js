module.exports = {
  'Demo Test Wastetopia' : function(client) {
    client
      .url('http://wastetopia.herokuapp.com')
      .waitForElementVisible('body', 1000);
    client.assert.containsText('body',"HomePage");
    client.end();
  }
}
