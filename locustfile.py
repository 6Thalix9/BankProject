from locust import HttpUser, task, between
import random

class MyUser(HttpUser):
    wait_time = between(1, 5)
    host = "http://localhost:8001"  # Ensure there is no trailing slash

    @task
    def perform_requests(self):
        account_id1 = random.randint(1, 100)
        account_id3 = random.randint(1, 10)
        account_id4 = random.randint(1, 10)

        # Send GET request
        self.client.get(f"/api/users/{account_id1}")

        # Send first POST request with amount
        self.client.post(f"/api/transitions/{account_id3}/transfer/{account_id4}", json={"amount": 50})

        # Send second POST request with receiver_id and amount
        self.client.post(f"/api/transitions/{account_id4}/deposit", json={"receiver_id": account_id4, "amount": 50})