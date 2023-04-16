const bycrypt = require('bcrypt');
const { user } = require('../../../models');
const validator = require('fastest-validator');
const v = new validator();

module.exports = async (req, res) => {
    const schema = {
        name: 'string|empty:false',
        email: 'email|empty:false',
        password: 'string|min:8',
        profession: 'string|optional',
    }

    const validate = v.validate(req.body, schema);

    // validate password length
    if (validate.length) {
        return req.status(400).json({
            status: 'error',
            message: validate
        });

        
    }
}