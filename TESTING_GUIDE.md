# Testing Guide - PDF Generation & Admin Panel

## Quick Test Checklist

### ✅ PDF Generation Testing

1. **Test Form Submission**:
   ```bash
   # Start servers
   php -S localhost:8000 -t api
   npm run dev
   ```
   
   - Navigate to `http://localhost:5173`
   - Fill out the form with test data
   - Include Bengali text in name fields
   - Upload a photo
   - Submit the form
   - Verify success message appears

2. **Verify PDF Generation**:
   - Check `/storage/submissions/YYYY/MM/` directory
   - Find the generated PDF file
   - Open the PDF and verify:
     - All form data is present
     - Bengali text renders correctly
     - Photo is embedded
     - Two pages are generated
     - Layout matches the template

3. **Test Edge Cases**:
   - Submit form with only required fields
   - Submit form with all fields filled
   - Test with long text in address fields
   - Test with special characters
   - Test without photo upload

### ✅ Admin Panel Testing

1. **Test Login**:
   - Navigate to `http://localhost:5173/admin`
   - Should redirect to `/admin/login`
   - Try invalid credentials → Should show error
   - Login with `admin` / `admin123` → Should redirect to dashboard

2. **Test Dashboard**:
   - Verify statistics display correctly
   - Check "Total Submissions" count
   - Click "View All Submissions" → Should navigate to list

3. **Test Submissions List**:
   - Verify submissions appear in table
   - Check pagination works (if > 20 submissions)
   - Test "View" button → Should show detail page
   - Test "Download PDF" button → Should download PDF

4. **Test Search & Filter**:
   - Enter a name in search box → Click "Search"
   - Verify filtered results
   - Select date range → Click "Search"
   - Verify date-filtered results
   - Click "Clear Filters" → Should show all submissions

5. **Test Submission Detail**:
   - Click "View" on any submission
   - Verify all form data displays correctly
   - Click "Download PDF" → Should download PDF
   - Click "Back to List" → Should return to list

6. **Test Protected Routes**:
   - Logout or clear localStorage
   - Try to access `/admin/dashboard` → Should redirect to login
   - Try to access `/admin/submissions` → Should redirect to login

7. **Test Session Timeout**:
   - Login to admin panel
   - Wait 24 hours (or modify timeout in config for testing)
   - Try to access protected route → Should redirect to login

## Manual Testing Scenarios

### Scenario 1: Complete Form Submission Flow

1. Open form at `http://localhost:5173`
2. Fill Step 1 (Personal Info):
   - Name (Bengali): আব্দুল করিম
   - Name (English): ABDUL KARIM
   - Father's Name: মোহাম্মদ আলী
   - Mother's Name: ফাতেমা বেগম
   - Mobile: 01712345678
   - Blood Group: A+
   - NID: 1234567890123
   - Birth Date: 01-01-2000
   - Upload photo
   - Fill addresses
3. Click "Next"
4. Fill Step 2 (Education):
   - SSC: Year 2015, Board Dhaka, Group Science, Institution ABC School
   - HSC: Year 2017, Board Dhaka, Group Science, Institution XYZ College
   - Graduation: Year 2021, University DU, Subject CSE, Institution Dhaka University
5. Click "Next"
6. Fill Step 3 (Declaration):
   - Movement Role: Participated in protests
   - Aspirations: Want to contribute to student welfare
7. Click "Submit"
8. Verify success modal appears
9. Check storage directory for PDF

### Scenario 2: Admin Panel Complete Flow

1. Navigate to `http://localhost:5173/admin/login`
2. Login with admin credentials
3. View dashboard statistics
4. Click "View All Submissions"
5. Search for a submission by name
6. Click "View" on a submission
7. Review all submission details
8. Download PDF from detail page
9. Go back to list
10. Download PDF from list page
11. Test date range filter
12. Logout

### Scenario 3: Error Handling

1. **Invalid Login**:
   - Try wrong username → Should show error
   - Try wrong password → Should show error

2. **Form Validation**:
   - Try to submit empty form → Should show validation errors
   - Try to proceed to next step with missing required fields → Should prevent

3. **Network Errors**:
   - Stop backend server
   - Try to submit form → Should show error message
   - Try to login to admin → Should show error message

## Automated Testing Commands

```bash
# Test PDF generation (if you have test scripts)
php tests/PDFGeneratorTest.php

# Test admin authentication
php tests/AdminControllerTest.php

# Test storage manager
php tests/StorageManagerTest.php

# Run all backend tests
composer test

# Run frontend tests
npm test
```

## Expected Results

### PDF Generation
- ✅ PDF file created in `/storage/submissions/YYYY/MM/`
- ✅ JSON file created with same name
- ✅ PDF contains all form data
- ✅ Bengali text renders correctly
- ✅ Photo is embedded
- ✅ Two pages generated

### Admin Panel
- ✅ Login works with correct credentials
- ✅ Dashboard shows correct statistics
- ✅ Submissions list displays all submissions
- ✅ Search filters submissions correctly
- ✅ Date range filter works
- ✅ Pagination works (if applicable)
- ✅ Submission detail shows all data
- ✅ PDF download works from both list and detail
- ✅ Protected routes redirect to login when not authenticated
- ✅ Logout clears session

## Common Issues & Solutions

### Issue: PDF not generating
**Solution**: 
- Check PHP error logs
- Verify TCPDF is installed: `composer show tecnickcom/tcpdf`
- Check write permissions on `/storage` directory

### Issue: Bengali text not showing in PDF
**Solution**:
- Verify internet connection (for Google Fonts CDN)
- Check HTML template has correct font link
- Ensure UTF-8 encoding in PHP files

### Issue: Admin login not working
**Solution**:
- Check session configuration in PHP
- Verify admin credentials in `config/admin.php`
- Check browser console for errors
- Clear browser cache and localStorage

### Issue: Search not returning results
**Solution**:
- Verify submissions exist in storage
- Check search query format
- Review StorageManager search logic
- Check PHP error logs

### Issue: PDF download fails
**Solution**:
- Verify PDF file exists in storage
- Check file permissions
- Review browser console for errors
- Check API endpoint response

## Performance Testing

### Load Testing
```bash
# Test with multiple concurrent submissions
# Use Apache Bench or similar tool
ab -n 100 -c 10 http://localhost:8000/api/submit
```

### PDF Generation Speed
- Single PDF: Should generate in < 2 seconds
- With photo: Should generate in < 3 seconds
- Large form data: Should generate in < 5 seconds

### Admin Panel Response Times
- Login: < 500ms
- List submissions: < 1s (for 100 submissions)
- Search: < 1s
- PDF download: < 2s

## Security Testing

### Test Authentication
- [ ] Cannot access admin routes without login
- [ ] Session expires after timeout
- [ ] Password is hashed in database
- [ ] Invalid credentials are rejected

### Test Authorization
- [ ] Only authenticated users can access admin endpoints
- [ ] Session token is validated on each request
- [ ] Logout properly destroys session

### Test Input Validation
- [ ] XSS attempts are sanitized
- [ ] SQL injection attempts are prevented
- [ ] File upload validates file types
- [ ] File upload validates file sizes

## Browser Compatibility

Test on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Chrome (Android)
- [ ] Mobile Safari (iOS)

## Accessibility Testing

- [ ] Keyboard navigation works
- [ ] Screen reader compatible
- [ ] Color contrast meets WCAG AA
- [ ] Form labels are properly associated
- [ ] Error messages are announced

## Final Checklist

Before deploying to production:

- [ ] Change default admin password
- [ ] Test all features work correctly
- [ ] Verify PDF generation with real data
- [ ] Test admin panel with multiple users
- [ ] Check error handling
- [ ] Verify security measures
- [ ] Test on different browsers
- [ ] Test on mobile devices
- [ ] Review and test backup procedures
- [ ] Document any custom configurations

## Support

If you encounter issues:
1. Check PHP error logs: `tail -f /var/log/php/error.log`
2. Check browser console for JavaScript errors
3. Review API responses in Network tab
4. Check file permissions on storage directory
5. Verify all dependencies are installed

## Success Criteria

The implementation is successful when:
- ✅ Users can submit forms and receive PDFs
- ✅ PDFs contain all form data correctly formatted
- ✅ Bengali text renders properly in PDFs
- ✅ Admins can login and access dashboard
- ✅ Admins can view, search, and download submissions
- ✅ All security measures are in place
- ✅ System handles errors gracefully
- ✅ Performance meets requirements
