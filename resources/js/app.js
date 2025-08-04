import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
                const bellButton = document.getElementById('notificationToggle');
                const container = document.getElementById('notificationContainer');

                function toggleNotification() {
                    container.style.display = (container.style.display === 'none' || container.style.display === '') ?
                        'block' :
                        'none';
                }

                // Toggle on bell click
                if (bellButton && container) {
                    bellButton.addEventListener('click', function(event) {
                        event.stopPropagation(); // prevent event from bubbling up
                        toggleNotification();
                    });

                    // Hide when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!container.contains(event.target) && event.target !== bellButton && !bellButton
                            .contains(event.target)) {
                            container.style.display = 'none';
                        }
                    });
                }
            });
            document.querySelectorAll('.view-request').forEach(button => {
                button.addEventListener('click', function() {
                    // Basic fields
                    document.getElementById('modalType').textContent = this.dataset.type || '-';
                    document.getElementById('modalDuration').textContent = this.dataset.duration || '-';
                    document.getElementById('modalReason').textContent = this.dataset.reason || '-';

                    // Format start date and time
                    const startDate = this.dataset.startDate || '-';
                    const startTime = this.dataset.startTime || '';
                    document.getElementById('modalStart').innerHTML = `
                    ${startDate}
                    ${startTime ? `<span class="badge bg-info text-white ms-2 text-capitalize">${startTime}</span>` : ''}
                `;

                    // Format end date and time
                    const endDate = this.dataset.endDate || '-';
                    const endTime = this.dataset.endTime || '';
                    document.getElementById('modalEnd').innerHTML = `
                    ${endDate}
                    ${endTime ? `<span class="badge bg-info text-white ms-2 text-capitalize">${endTime}</span>` : ''}
                `;

                    // Status badge
                    const status = (this.dataset.status || '').toLowerCase();
                    const statusMap = {
                        planned: 'secondary',
                        accepted: 'success',
                        requested: 'warning',
                        rejected: 'danger',
                        cancellation: 'danger',
                        canceled: 'danger'
                    };
                    const badgeClass = statusMap[status] || 'light';

                    document.getElementById('modalStatus').innerHTML = `
                    <span class="badge bg-${badgeClass} text-white text-capitalize">${status}</span>
                `;
                });
            });