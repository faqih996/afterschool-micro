const bcrypt = require('bcrypt');
const { User } = require('../../../models');
const validator = require('fastest-validator');
const v = new validator();

module.exports = async (req, res) => {
    // deklarasi rule
    const schema = {
        email: 'email|empty:false',
        password: 'string|min:8'
    }

    // validasi
    const validate = v.validate(req.body, schema);
    
    //validasi length
    if (validate.length) {
        return res.status(400).json({
            status: 'error',
            message: validate
        });
    }

    // validasi akun
    const user = await User.findOne({
        where: { email: req.body.email }
    });

    if (!user) {
        return res.status(404).json({
            status: 'error',
            message: 'User not found'
        });
    }

    const isValidPassword = await bcrypt.compare(req.body.password, user.password);
    if (!isValidPassword) {
        return res.status(404).json({
            status: 'error',
            message: 'User not found'
        });
    }

    res.json({
        status: 'success',
        data: {
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role,
            avatar: user.avatar,
            profession: user.profession
        }
    });
}