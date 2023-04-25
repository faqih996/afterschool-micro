const express = require('express');
const router = express.Router();

const myCoursesHandler = require('./handler/myCourses');

router.post('/', myCoursesHandler.create);
router.get('/', myCoursesHandler.get);

module.exports = router;