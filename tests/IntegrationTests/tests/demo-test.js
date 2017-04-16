const url = require('../url.js');
module.exports = {
  'Demo Test Wastetopia' : function(client) {
    client
      .url(url.ROOT)
      .waitForElementVisible('body', 1000);
    client.assert.containsText('body',"HomePage");
    client.end();
  }
}
