const url = require('../url.js');
module.exports = {
  'Test Receiving Tab Opens' : function(client) {
    client
      .url(url.ROOT + '/messages')
      .waitForElementVisible('body', 1000);
    client.waitForElementVisible('a[href="#receiving-tab"]',1000);
    client.click('a[href="#receiving-tab"]');
    client.pause(1000);
    client.assert.cssClassPresent('#receiving-tab', 'active');
    client.assert.cssClassPresent('#receiving-tab', 'in');
    client.end();
  }
}
