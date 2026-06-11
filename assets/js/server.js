const express = require('express');
const nodemailer = require('nodemailer');
const cors = require('cors');
const emailConfig = require('./emailConfig');

const app = express();

// Enable CORS and JSON parsing
app.use(cors());
app.use(express.json());

// Configure Nodemailer transporter
const transporter = nodemailer.createTransport({
    host: 'smtp.gmail.com',
    port: 465,
    secure: true, // true for port 465, false for other ports
    auth: {
        user: emailConfig.email,
        pass: emailConfig.appPassword
    }
});

// Endpoint to send the CTL Activity report email
app.post('/send-email', (req, res) => {
    const { to, cc, subject, html } = req.body;

    if (!to) {
        return res.status(400).json({ success: false, error: 'Recipient email ("to") is required.' });
    }

    const mailOptions = {
        from: `"GMIU IT Department" <${emailConfig.email}>`,
        to: to,
        cc: cc && cc.length > 0 ? cc.join(', ') : '',
        subject: subject || 'CTL Activity Dashboard Report',
        html: html || '<p>No content provided.</p>'
    };

    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            console.error('SMTP sending error:', error);
            return res.status(500).json({ success: false, error: error.message });
        }
        console.log('Email sent successfully:', info.response);
        res.json({ success: true, message: 'Email sent successfully!', messageId: info.messageId });
    });
});

const PORT = 3000;
app.listen(PORT, () => {
    console.log(`========================================================`);
    console.log(` GMIU IT Email Server running on http://localhost:${PORT}`);
    console.log(` Ready to receive reports from ctlactivity.html...`);
    console.log(`========================================================`);
});
