This is the Rowan IEEE SAC 17 iOS App.

Created by Rowan University's SAC 2017 development commitee. Primary developers for this iOS version were:

Nate Hoffman

Johnathan Saunders

If being released again in the future, the provisioning profile and bundle ID (which also requires a new key from Firebase) would need to be updated to resolve conflicts.

Features should be added as an issue, assigned to somebody, and then a branch should be created.

Branches and how to handle them:

master - Production (very near ready for release and/or between release versions). This should be a stable branch.

release-X.X - When submitting a version to the App Store, branch from the master. This allows there to always be a copy of the code from each version.

development - The main location where features stem from and go back into (should be mostly stable, but some bugs are okay as long as other features can be based off of it). Merges into the master.

feature-YYY - Stem from the development branch to work on a feature. If the feature needs work done in the future, pull the latest changes from the development branch and continue working in this branch.
