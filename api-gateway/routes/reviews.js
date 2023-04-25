const express = require('express');
const router = express.Router();

const reviewsHandler = require('./handler/reviews');

router.post('/', reviewsHandler.create);

module.exports = router;