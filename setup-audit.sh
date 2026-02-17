#!/usr/bin/env bash

# Change Tracking Module - Setup Script
# This script helps you set up the audit tracking system

echo "================================"
echo "Change Tracking Module Setup"
echo "================================"
echo ""

# Step 1: Run Migration
echo "Step 1: Running database migration..."
php artisan migrate

if [ $? -eq 0 ]; then
    echo "✓ Migration completed successfully"
else
    echo "✗ Migration failed. Please check your database configuration."
    exit 1
fi

echo ""

# Step 2: List models to track
echo "Step 2: Review models that should be tracked"
echo ""
echo "The Auditable trait should be added to models you want to track:"
echo "  - Add 'use Auditable;' to your model class"
echo "  - Ensure the model has a proper \$fillable array"
echo ""
echo "Example models in your project:"
echo "  - app/Models/User.php (already added)"
echo ""
echo "To add tracking to other models:"
echo "  1. Open the model file"
echo "  2. Add: use App\\Traits\\Auditable;"
echo "  3. Add: use Auditable; in the class definition"
echo ""

# Step 3: Verify routes
echo "Step 3: Verifying routes..."
echo ""
echo "The following routes have been added:"
echo "  GET  /audits              - View all audits (admin only)"
echo "  GET  /audits/show         - View change history for a model"
echo "  GET  /audits/export       - Export audits to CSV"
echo ""

# Step 4: Test the system
echo "Step 4: Testing the system"
echo ""
echo "To test if audits are being recorded:"
echo ""
echo "php artisan tinker"
echo "> \$user = \\App\\Models\\User::first();"
echo "> \$user->update(['name' => 'Test']);"
echo "> \\App\\Models\\Audit::latest()->first();"
echo ""

echo "================================"
echo "Setup Complete!"
echo "================================"
echo ""
echo "Next steps:"
echo "1. Review AUDIT_DOCUMENTATION.md for full documentation"
echo "2. Add Auditable trait to models you want to track"
echo "3. Update User model if you have custom admin logic"
echo "4. Test by making a change and visiting /audits"
echo ""
