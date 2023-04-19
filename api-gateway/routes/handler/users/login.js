const apiAdapter = require('../../apiAdapter');
const jwt = require('jsonwebtoken');

// definition
const {
    URL_SERVICE_USER,
    JWT_SECRET,
    JWT_SECRET_REFRESH_TOKEN,
    JWT_ACCESS_TOKEN_EXPIRED,
    JWT_REFRESH_TOKEN_EXPIRED
} = process.env;

const api = apiAdapter(URL_SERVICE_USER);

module.exports = async (req, res) => {
    try {

        // set request from body
        const user = await api.post('/users/login', req.body);
        // refer to API data 
        const data = user.data.data;

        // set expired JWT 
        const token = jwt.sign({ data }, JWT_SECRET, { expiresIn: JWT_ACCESS_TOKEN_EXPIRED });
        
        // set expired refresh JWT
        const refreshToken = jwt.sign({ data }, JWT_SECRET_REFRESH_TOKEN, { expiresIn: JWT_REFRESH_TOKEN_EXPIRED });

        // send token to table refresh token
        await api.post('/refresh_tokens', { refresh_token: refreshToken, user_id: data.id });

        // send status to API
        return res.json({
            status: 'success',
            data: {
                token,
                refresh_token: refreshToken
            }
        });

    } catch (error) {

        // check if server working
        if (error.code === 'ECONNREFUSED') {
            return res.status(500).json({ status: 'error', message: 'service unavailable' });
        }

        // if server ok
        const { status, data } = error.response;
        return res.status(status).json(data);
    }
}