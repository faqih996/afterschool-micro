const express = require('express');
const router = express.Router();
const isBase64 = require('is-base64');
const base64Img = require('base64-img');
const fs = require('fs');

// model sequelize yang sudah dibuat
const { Media } = require('../models');

// API view all image
router.get('/', async(req, res) => {
  const media = await Media.findAll({
    attributes: ['id', 'image']
  });

  const mappedMedia = media.map((m) => {
    m.image = `${req.get('host')}/${m.image}`;
    return m;
  })

  return res.json({
    status: 'success',
    data: mappedMedia
  });
});


// API upload image
router.post('/', (req, res) => {
  const image = req.body.image;

  // validasi image 
  if (!isBase64(image, { mimeRequired: true })) {
    return res.status(400).json({ status: 'error', message: 'invalid base64' });
  }

  // vaidasi image dan merubah nama berdasarkan tanggal
  base64Img.img(image, './public/images', Date.now(), async (err, filepath) => {
    if (err) {
      // cek jika error
      return res.status(400).json({ status: 'error', message: err.message });
    }

    // men generate filename dan memilih folder
    const filename = filepath.split("\\").pop().split("/").pop();

    // upload image sesuai filename
    const media = await Media.create({ image: `images/${filename}` });

    // memberikan informasi jika sukses
    return res.json({
      status: 'success',
      data: {
        id: media.id,
        image: `${req.get('host')}/images/${filename}`
      }
    });

  })
});


// API Delete Image
router.delete('/:id', async (req, res) => {
  const id = req.params.id;

  const media = await Media.findByPk(id);

  if (!media) {
    return res.status(404).json({ status: 'error', message: 'media not found' });
  }

  fs.unlink(`./public/${media.image}`, async (err) => {
    if (err) {
      return res.status(400).json({ status: 'error', message: err.message });
    }
    
    await media.destroy();

    return res.json({
      status: 'success',
      message: 'image deleted'
    });
  });
});


module.exports = router;
