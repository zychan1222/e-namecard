<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VCard Download Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid #ccc;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .details {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .download-btn {
            display: block;
            width: 200px;
            text-align: center;
            background-color: #5044e4;
            color: white;
            padding: 14px 20px;
            margin: 20px auto;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none; 
        }
        .download-btn:hover {
            background-color: #6255eb; 
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            background-color: #202c34; 
            padding: 10px; 
            border-radius: 5px; 
        }
        .header img {
            display: block;
            margin: 0 auto;
        }
        .download-section {
            text-align: center;
            margin-top: 40px;
        }
        .download-title {
            margin-bottom: 10px;
            color: #333; 
        }
        .explanation {
            background-color: #f9f9f9; 
            border-radius: 5px; 
            padding: 20px; 
            margin-top: 20px; 
            border: 1px solid #ccc;
        }
        .explanation h2 {
            color: #333;
            margin-bottom: 10px; 
        }
        .explanation p {
            color: #666; 
            margin-bottom: 10px; 
        }
        .explanation ol {
            margin-bottom: 10px; 
        }
        .explanation li {
            color: #666; 
            margin-bottom: 5px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <img class="h-20 w-25" src="{{ asset('storage/logo-no-bg.png') }}" alt="Your Company">
    </div>
    <h1>User Details</h1>
    <div class="details">
        <img class="profile-pic" src="{{ $employee->profile_pic ? asset('storage/' . $employee->profile_pic) : asset('storage/default-user.jpg') }}" alt="Profile Picture">
        <p><strong>Name:</strong> {{ $employee->name }}</p>
        <p><strong>CN Name:</strong> {{ $employee->name_cn }}</p>
        <p><strong>Email:</strong> {{ $employee->email }}</p>
        <p><strong>Phone:</strong> {{ $employee->phone }}</p>
        <p><strong>Designation:</strong> {{ $employee->designation }}</p>
        <p><strong>Department:</strong> {{ $employee->department }}</p>
        <p><strong>Company Name:</strong> {{ $employee->company_name }}</p>
    </div>

    <div class="download-section">
        <h2 class="download-title">Download Contact</h2>
        <a href="{{ secure_url('download-vcard', ['name' => $employee->name, 'phone' => $employee->phone]) }}" class="download-btn">Download VCard</a>
    </div>

    <div class="explanation">
        <h2>What is a VCard?</h2>
        <p>When you download a VCard, think of it like grabbing a digital business card from the internet. Just like a physical business card holds contact information, a VCard does the same, but in a digital format.</p>
        <p>Here's how it works:</p>
        <ol>
            <li><strong>Clicking the Download Button:</strong> When you click the "Download VCard" button, you're essentially asking the internet to give you a file that contains contact details.</li>
            <li><strong>VCard Format:</strong> The file you're downloading is called a VCard, often saved with a ".vcf" extension. This file is specifically designed to store contact information like names, phone numbers, email addresses, and more.</li>
            <li><strong>Saving the VCard:</strong> Once you click the download button, your device will save the VCard file. It's like putting a digital business card into your device's address book.</li>
            <li><strong>Using the VCard:</strong> After it's saved, you can open the VCard with programs like your phone's contacts app or email client. It will automatically read the information and add it to your contacts list, just like when you add a new contact manually.</li>
        </ol>
        <p>So, downloading a VCard is like grabbing a digital version of someone's contact details from the internet, which you can then easily add to your own contacts list on your device.</p>
    </div>
</body>
</html>
