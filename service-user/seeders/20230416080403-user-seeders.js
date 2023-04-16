'use strict';
const bycript = require('bcrypt');

module.exports = {
  async up (queryInterface, Sequelize) {
      
    await queryInterface.bulkInsert('users', [
      {
        name: 'Starter',
        profession: "Admin Micro",
        role: "admin",
        email: "faqih.syakir11@gmail.com",
        password: await bycript.hash('faqih996', 10),
        created_at: new Date(),
        updated_at: new Date(),
      },
      {
        name: 'Santuy',
        profession: "Back End Developher",
        role: "student",
        email: "user@mail.com",
        password: await bycript.hash('syakir996', 10),
        created_at: new Date(),
        updated_at: new Date(),
      }
    ]);
  },

  async down (queryInterface, Sequelize) {
    await queryInterface.bulkDelete('users', null, {});
  }
  
};
