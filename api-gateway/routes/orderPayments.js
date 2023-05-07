const express = require('express');
const router = express.Router();

const orderPaymentsHandler = require('./handler/order-payments');

router.get('/', orderPaymentsHandler.getOrder);

module.exports = router;