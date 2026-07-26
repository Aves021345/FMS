Role                    Username                    Password
Admin	                admin	                    Admin@123
Finance	                finance	                    Finance@123
Accountant	            accountant	                Accountant@123
Auditor	                auditor	                    Auditor@123


Role constants ready to use:

Constant	                              Value
Auth::ROLE_ADMIN	                      System Administrator
Auth::ROLE_FINANCE	                      Finance Officer
Auth::ROLE_ACCOUNTANT	                  Accountant
Auth::ROLE_AUDITOR	                      Internal Auditor


Map the roles to your Financial Management modules

Module                                    Main role
General Ledger                            Accountant
Accounts Payable (AP)                     Finance Officer / Accountant
Accounts Receivable (AR)                  Finance Officer / Accountant
Disbursement Management                   Finance Officer
Collection Management                     Finance Officer
Budget Management                         Finance Officer (or Admin)
Cash Management                           Finance Officer
Tax Management                            Accountant
Financial Reporting & Analytics           Accountant / Auditor
Audit Trail                               Internal Auditor