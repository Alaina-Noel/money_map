# Budget Tracker

<p align="center"><img src="https://via.placeholder.com/400x150?text=Budget+Tracker" width="400" alt="Budget Tracker Logo"></p>

<p align="center">
<img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status">
<img src="https://img.shields.io/badge/license-MIT-blue" alt="License">
</p>

## About Budget Tracker

Budget Tracker is a simple and intuitive application designed to help users manage their personal finances. It allows users to track income, expenses, savings, and budgets in an organized and secure way.

### Features
- Create and categorize budgets (e.g., Rent, Groceries, Entertainment).
- Track income through paychecks and other sources.
- Add line items for individual expenses or earnings.
- View summaries of actual vs. expected spending and income.
- Multi-user support with data isolation.

---

## Getting Started

### Prerequisites
Ensure you have the following installed:
- PHP >= 8.0
- Composer
- MySQL or SQLite

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/budget-tracker.git
   cd budget-tracker
1. composer install
1. cp .env.example .env
1. php artisan key:generate
1. set your .env database variables
1. php artisan migrate
1. php artisan serve
1. Visit http://localhost:8000 in your browser.


