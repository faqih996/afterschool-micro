const bcrypt = require('bcrypt');
const { User } = require('../../../models');
const Validator = require('fastest-validator');
const v = new Validator();

module.exports = async (req, res) => {
    const schema = {
        name: 'string|empty:false',
        email: 'email|empty:false',
        password: 'string|min:8',
        profession: 'string|optional',
        avatar: 'string'
    };

    // validasi request
    const validate = v.validate(req.body, schema);
    if (validate.lenght) {
        return res.status(400).json({
            status: 'error',
            message: validate
        });
    }

    // Check User Id
    const id = req.params.id;
    const user = await User.findByPk(id);
    if (!user) {
        return res.status(404).json({
            status: 'error',
            message: 'User not found'
        });
    }

    // Cek Email
    const email = req.body.email;
    if (email) {
        const CheckEmail = await User.findOne({
            where: { email }
        });

        if (CheckEmail && email !== user.email) {
            return res.status(409).json({
                status: 'error',
                message: 'Email already exist'
            })
        }
    }

    // cek password
    const password = await bcrypt.hash(req.body.password, 10);
    const {
        name, profession, avatar
    } = req.body;

    await user.update({
        name,
        email,
        password,
        profession,
        avatar
    });

    return res.json({
        status: 'success',
        data: {
            id: user.id,
            name,
            email,
            password,
            profession,
            avatar
        }
    });

}