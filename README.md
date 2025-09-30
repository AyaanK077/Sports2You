# Sports2You 🏐🏀⚽

![Built with HTML](https://img.shields.io/badge/Frontend-HTML5-E34F26?style=for-the-badge&logo=html5)  
![CSS](https://img.shields.io/badge/Styling-CSS3-1572B6?style=for-the-badge&logo=css3)  
![JavaScript](https://img.shields.io/badge/Scripting-JavaScript-F7DF1E?style=for-the-badge&logo=javascript)  
![Database MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql)  

**Sports2You** (inspired by When2Meet) is a **sports scheduling and coordination system** that allows college students to share their availability, match times with friends, and set up games quickly.  
It was developed as part of a **Database Systems course project**, with an emphasis on **3NF design, functional dependencies, and SQL-based implementation**.  

---

## Features

- **Availability input**: students enter time slots when they are free  
- **Automatic match finder**: system highlights overlapping free times for groups  
- **Event creation**: users can create and join sports sessions  
- **Database-backed design**: relational schema optimized up to **3NF**  
- **Clean UI**: simple HTML/CSS/JS frontend for input and visualization  

---

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP (for connecting frontend to database)  
- **Database:** MySQL (schema design, SQL queries, triggers)  
- **Hosting:** Localhost (XAMPP) during development  

---

## Database Design

- **Normalization:** All relations were converted to **3NF** to eliminate redundancy  
- **Functional Dependencies:** Clearly documented and used to derive schema  
- **Schema includes:**  
  - Users (student info, login)  
  - Availability (time slots)  
  - Sports Events (game details, participants)  
  - Matches (overlap results)  

---

## Getting Started

1. Clone this repo:  
   ```bash
   git clone https://github.com/ayaank077/Sports2You.git
   cd Sports2You
