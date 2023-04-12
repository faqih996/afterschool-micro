const express = require('express');
const router = express.Router();

router.post('/', (req, res) => {
  res.send('test 12345')
});

module.exports = router;
