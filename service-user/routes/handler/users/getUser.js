const { User } = require('../../../models');

module.exports = async (req, res) => {
    const id = req.params.id;

    // memanggil berdasarkan id
    const user = await User.findByPk(id, {
        // memanggil data yang dibutuhkan
        attributes: ['id', 'name', 'email', 'role', 'profession', 'avatar']
    })

    if (!user) {
        return res.status(404).json({
            status: 'error',
            message: 'User not found'
        });
    }

    return res.json({
        status: 'success',
        data: user
    });
}